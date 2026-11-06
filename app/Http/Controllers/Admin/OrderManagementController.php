<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\DeliveryDocument;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Vehicle;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer.user', 'activeAssignment.driver.user', 'activeAssignment.vehicle'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('company_name', 'like', "%{$search}%")
                         ->orWhere('contact_person', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load([
            'customer.user',
            'assignments.driver.user',
            'assignments.vehicle',
            'assignments.proof',
            'documents',
            'financialTransaction',
            'rating'
        ]);

        // Available active drivers
        $availableDrivers = Driver::where('verification_status', 'verified')
            ->where('availability_status', 'Aktif')
            ->whereDoesntHave('assignments', function ($q) {
                $q->whereIn('status', ['Assigned', 'Accepted', 'In Transit', 'Filling Water']);
            })
            ->with('user')
            ->get();

        // Available ready vehicles
        $availableVehicles = Vehicle::where('status', 'Ready')
            ->whereDoesntHave('assignments', function ($q) {
                $q->whereIn('status', ['Assigned', 'Accepted', 'In Transit', 'Filling Water']);
            })
            ->get();

        return view('admin.orders.show', compact('order', 'availableDrivers', 'availableVehicles'));
    }

    public function approve(Request $request, Order $order)
    {
        $validated = $request->validate([
            'driver_id' => ['required', 'exists:drivers,id'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
        ]);

        $driver = Driver::with('user')->findOrFail($validated['driver_id']);
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        // Check vehicle status
        if ($vehicle->status !== 'Ready') {
            return back()->with('error', "Armada {$vehicle->plate_number} sedang {$vehicle->status} dan tidak dapat ditugaskan.");
        }

        // Check vehicle capacity
        // If order volume > vehicle capacity and not multiple trips assigned
        if ($order->volume > $vehicle->capacity && $order->trips_count <= 1) {
            return back()->with('error', "Kapasitas armada ({$vehicle->capacity} L) kurang dari volume pesanan ({$order->volume} L). Silakan pilih armada berkapasitas lebih besar.");
        }

        // Check driver collision
        $driverBusy = Assignment::where('driver_id', $driver->id)
            ->whereIn('status', ['Assigned', 'Accepted', 'In Transit', 'Filling Water'])
            ->exists();

        if ($driverBusy) {
            return back()->with('error', "Driver {$driver->user->name} sedang memiliki tugas aktif lain.");
        }

        // Check vehicle collision
        $vehicleBusy = Assignment::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['Assigned', 'Accepted', 'In Transit', 'Filling Water'])
            ->exists();

        if ($vehicleBusy) {
            return back()->with('error', "Armada {$vehicle->plate_number} sedang ditugaskan pada order lain.");
        }

        DB::transaction(function () use ($order, $driver, $vehicle) {
            // Create assignment
            $assignment = Assignment::create([
                'order_id' => $order->id,
                'driver_id' => $driver->id,
                'vehicle_id' => $vehicle->id,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'status' => 'Assigned',
            ]);

            // Update order status to Diproses
            $order->update([
                'status' => 'Diproses',
                'pending_reason' => null,
            ]);

            // Mark vehicle as On Duty
            $vehicle->update(['status' => 'On Duty']);

            // Auto-generate Surat Izin Jalan
            $documentNumber = DeliveryDocument::generateDocumentNumber();
            DeliveryDocument::create([
                'order_id' => $order->id,
                'assignment_id' => $assignment->id,
                'document_number' => $documentNumber,
                'revision' => 1,
                'issued_at' => now(),
                'issued_by' => Auth::id(),
            ]);

            ActivityLog::log(
                'ORDER_APPROVED',
                "Menyetujui order {$order->order_number}, menugaskan Driver {$driver->user->name} dan armada {$vehicle->plate_number}. Surat Jalan {$documentNumber} diterbitkan."
            );
        });

        // Notifications
        NotificationService::notifyDriver(
            $driver->user,
            'Penugasan Pengiriman Baru!',
            "Anda mendapatkan tugas baru untuk order {$order->order_number} ({$order->volume} L ke {$order->customer->company_name}). Harap segera konfirmasi.",
            route('driver.jobs.index'),
            'info',
            $order->id
        );

        NotificationService::notifyCustomer(
            $order,
            'Pesanan Disetujui',
            "Pesanan {$order->order_number} telah disetujui. Armada {$vehicle->plate_number} dan Driver {$driver->user->name} telah ditugaskan.",
            route('customer.orders.show', $order->id),
            'success'
        );

        return back()->with('success', "Pesanan {$order->order_number} berhasil disetujui. Driver & Armada telah ditugaskan dan Surat Izin Jalan diterbitkan otomatis.");
    }

    public function pending(Request $request, Order $order)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $order->update([
            'status' => 'Pending',
            'pending_reason' => $validated['reason'],
        ]);

        NotificationService::notifyCustomer(
            $order,
            'Pesanan Ditunda',
            "Pesanan {$order->order_number} ditunda oleh Admin. Alasan: {$validated['reason']}",
            route('customer.orders.show', $order->id),
            'warning'
        );

        ActivityLog::log('ORDER_PENDING', "Menunda order {$order->order_number}. Alasan: {$validated['reason']}");

        return back()->with('warning', "Status pesanan {$order->order_number} diubah menjadi Pending dengan alasan tersimpan.");
    }

    public function reject(Request $request, Order $order)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        DB::transaction(function () use ($order, $validated) {
            $order->update([
                'status' => 'Dibatalkan',
                'rejection_reason' => $validated['reason'],
            ]);

            // Release any active assignment if existing
            if ($activeAssignment = $order->activeAssignment) {
                $activeAssignment->update(['status' => 'Cancelled']);
                $activeAssignment->vehicle->update(['status' => 'Ready']);
                $activeAssignment->driver->update(['availability_status' => 'Aktif']);
            }

            ActivityLog::log('ORDER_REJECTED', "Menolak order {$order->order_number}. Alasan: {$validated['reason']}");
        });

        NotificationService::notifyCustomer(
            $order,
            'Pesanan Ditolak',
            "Mohon maaf, pesanan {$order->order_number} ditolak oleh Admin. Alasan: {$validated['reason']}",
            route('customer.orders.show', $order->id),
            'danger'
        );

        return back()->with('info', "Pesanan {$order->order_number} berhasil ditolak.");
    }

    public function reassign(Request $request, Order $order)
    {
        $validated = $request->validate([
            'driver_id' => ['required', 'exists:drivers,id'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
        ]);

        $driver = Driver::with('user')->findOrFail($validated['driver_id']);
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        if ($vehicle->status !== 'Ready') {
            return back()->with('error', "Armada {$vehicle->plate_number} sedang {$vehicle->status}.");
        }

        if ($order->volume > $vehicle->capacity && $order->trips_count <= 1) {
            return back()->with('error', "Kapasitas armada ({$vehicle->capacity} L) kurang dari volume pesanan ({$order->volume} L).");
        }

        DB::transaction(function () use ($order, $driver, $vehicle) {
            // Cancel old active assignment
            if ($oldAssignment = $order->activeAssignment) {
                $oldAssignment->update(['status' => 'Cancelled']);
                $oldAssignment->vehicle->update(['status' => 'Ready']);
            }

            // Create new assignment
            $assignment = Assignment::create([
                'order_id' => $order->id,
                'driver_id' => $driver->id,
                'vehicle_id' => $vehicle->id,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'status' => 'Assigned',
            ]);

            // Update order status back to Diproses
            $order->update([
                'status' => 'Diproses',
                'pending_reason' => null,
            ]);

            $vehicle->update(['status' => 'On Duty']);

            // Create revised Surat Izin Jalan
            $revisionCount = DeliveryDocument::where('order_id', $order->id)->count() + 1;
            $documentNumber = DeliveryDocument::generateDocumentNumber();

            DeliveryDocument::create([
                'order_id' => $order->id,
                'assignment_id' => $assignment->id,
                'document_number' => $documentNumber,
                'revision' => $revisionCount,
                'issued_at' => now(),
                'issued_by' => Auth::id(),
            ]);

            ActivityLog::log(
                'ORDER_REASSIGNED',
                "Penugasan ulang order {$order->order_number} kepada Driver {$driver->user->name} & armada {$vehicle->plate_number}. Surat Jalan {$documentNumber} revisi {$revisionCount} diterbitkan."
            );
        });

        NotificationService::notifyDriver(
            $driver->user,
            'Penugasan Pengiriman Baru (Reassigned)',
            "Anda mendapatkan tugas pengiriman pesanan {$order->order_number} ({$order->volume} L).",
            route('driver.jobs.index'),
            'info',
            $order->id
        );

        NotificationService::notifyCustomer(
            $order,
            'Penugasan Armada Diperbarui',
            "Pesanan {$order->order_number} kini ditugaskan kepada armada {$vehicle->plate_number} dan driver {$driver->user->name}.",
            route('customer.orders.show', $order->id),
            'info'
        );

        return back()->with('success', "Penugasan ulang berhasil dilakukan. Surat Izin Jalan revisi telah dibuat.");
    }
}

