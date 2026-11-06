<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\DeliveryLocation;
use App\Models\DeliveryProof;
use App\Models\FinancialTransaction;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DriverJobController extends Controller
{
    public function index(Request $request)
    {
        $driver = Auth::user()->driver;
        $query = Assignment::where('driver_id', $driver->id)
            ->with(['order.customer', 'vehicle', 'proof'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jobs = $query->paginate(10)->withQueryString();

        return view('driver.jobs.index', compact('jobs'));
    }

    public function show(Assignment $assignment)
    {
        $this->authorizeDriver($assignment);

        $assignment->load([
            'order.customer.user',
            'vehicle',
            'document',
            'proof',
            'latestLocation',
            'locations'
        ]);

        return view('driver.jobs.show', compact('assignment'));
    }

    public function accept(Assignment $assignment)
    {
        $this->authorizeDriver($assignment);

        if ($assignment->status !== 'Assigned') {
            return back()->with('error', 'Status penugasan ini sudah bukan baru.');
        }

        DB::transaction(function () use ($assignment) {
            $assignment->update([
                'status' => 'Accepted',
                'accepted_at' => now(),
            ]);

            $assignment->driver->update(['availability_status' => 'Aktif']);
        });

        NotificationService::notifyAdmins(
            'Tugas Diterima Driver',
            "Driver {$assignment->driver->user->name} menerima pengiriman pesanan {$assignment->order->order_number}.",
            route('admin.orders.show', $assignment->order_id),
            'success',
            $assignment->order_id
        );

        ActivityLog::log('DRIVER_JOB_ACCEPTED', "Driver {$assignment->driver->user->name} menerima tugas order {$assignment->order->order_number}.", Auth::id());

        return back()->with('success', 'Tugas pengiriman berhasil diterima. Silakan bersiap menuju lokasi.');
    }

    public function reject(Request $request, Assignment $assignment)
    {
        $this->authorizeDriver($assignment);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        DB::transaction(function () use ($assignment, $validated) {
            $assignment->update([
                'status' => 'Rejected',
                'driver_reject_reason' => $validated['reason'],
            ]);

            // Release vehicle back to Ready
            $assignment->vehicle->update(['status' => 'Ready']);

            // Set order back to Pending so Admin can reassign
            $assignment->order->update([
                'status' => 'Pending',
                'pending_reason' => "Driver {$assignment->driver->user->name} menolak penugasan: {$validated['reason']}",
            ]);
        });

        NotificationService::notifyAdmins(
            'Driver Menolak Tugas',
            "Driver {$assignment->driver->user->name} menolak order {$assignment->order->order_number}. Alasan: {$validated['reason']}",
            route('admin.orders.show', $assignment->order_id),
            'danger',
            $assignment->order_id
        );

        ActivityLog::log('DRIVER_JOB_REJECTED', "Driver menolak order {$assignment->order->order_number}. Alasan: {$validated['reason']}", Auth::id());

        return redirect()->route('driver.dashboard')->with('info', 'Tugas pengiriman ditolak. Admin telah diberitahu untuk penugasan ulang.');
    }

    public function updateStatus(Request $request, Assignment $assignment)
    {
        $this->authorizeDriver($assignment);

        $action = $request->input('action');

        if ($action === 'start_transit') {
            DB::transaction(function () use ($assignment) {
                $assignment->update([
                    'status' => 'In Transit',
                    'started_at' => now(),
                ]);

                // Order status becomes Dikirim
                $assignment->order->update(['status' => 'Dikirim']);
                $assignment->vehicle->update(['status' => 'On Duty']);
                $assignment->driver->update(['availability_status' => 'On Delivery']);
            });

            NotificationService::notifyCustomer(
                $assignment->order,
                'Armada Sedang Menuju Lokasi Anda',
                "Truk tangki {$assignment->vehicle->plate_number} yang dikemudikan oleh {$assignment->driver->user->name} sedang dalam perjalanan menuju alamat Anda.",
                route('customer.orders.tracking', $assignment->order_id),
                'info'
            );

            NotificationService::notifyAdmins(
                'Pengiriman Dimulai',
                "Driver {$assignment->driver->user->name} memulai perjalanan untuk order {$assignment->order->order_number}.",
                route('admin.orders.show', $assignment->order_id),
                'info',
                $assignment->order_id
            );

            ActivityLog::log('DRIVER_START_TRANSIT', "Driver {$assignment->driver->user->name} memulai perjalanan order {$assignment->order->order_number}.", Auth::id());

            return back()->with('success', 'Perjalanan pengiriman dimulai. Status pesanan kini DIKIRIM.');
        }

        if ($action === 'start_filling') {
            $assignment->update([
                'status' => 'Filling Water',
                'arrived_at' => now(),
            ]);

            NotificationService::notifyCustomer(
                $assignment->order,
                'Armada Tiba di Lokasi - Pengisian Air',
                "Driver telah tiba di lokasi pengiriman dan sedang memulai proses pengisian air tangki.",
                route('customer.orders.show', $assignment->order_id),
                'info'
            );

            ActivityLog::log('DRIVER_START_FILLING', "Driver {$assignment->driver->user->name} tiba dan memulai pengisian air order {$assignment->order->order_number}.", Auth::id());

            return back()->with('success', 'Status diperbarui: Sedang melakukan pengisian air.');
        }

        return back()->with('error', 'Aksi status tidak valid.');
    }

    public function navigation(Assignment $assignment)
    {
        $this->authorizeDriver($assignment);

        $assignment->load(['order.customer', 'vehicle', 'latestLocation']);

        return view('driver.jobs.navigation', compact('assignment'));
    }

    public function updateGpsLocation(Request $request, Assignment $assignment)
    {
        $this->authorizeDriver($assignment);

        $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'speed' => ['nullable', 'numeric'],
            'heading' => ['nullable', 'numeric'],
        ]);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        // Save breadcrumb in delivery_locations
        DeliveryLocation::create([
            'assignment_id' => $assignment->id,
            'driver_id' => $assignment->driver_id,
            'latitude' => $lat,
            'longitude' => $lng,
            'speed' => $request->speed,
            'heading' => $request->heading,
            'recorded_at' => now(),
        ]);

        // Update driver's live position
        $assignment->driver->update([
            'current_latitude' => $lat,
            'current_longitude' => $lng,
            'last_location_update' => now(),
        ]);

        // Update vehicle's live position
        $assignment->vehicle->update([
            'current_latitude' => $lat,
            'current_longitude' => $lng,
        ]);

        return response()->json([
            'success' => true,
            'recorded_at' => now()->toIso8601String(),
        ]);
    }

    public function complete(Request $request, Assignment $assignment)
    {
        $this->authorizeDriver($assignment);

        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Store proof photo securely
        $path = $request->file('photo')->store('proofs', 'public');

        DB::transaction(function () use ($assignment, $validated, $path) {
            // 1. Create delivery proof
            DeliveryProof::create([
                'order_id' => $assignment->order_id,
                'assignment_id' => $assignment->id,
                'driver_id' => $assignment->driver_id,
                'photo_path' => $path,
                'recipient_name' => $validated['recipient_name'] ?? $assignment->order->recipient_name,
                'notes' => $validated['notes'] ?? 'Pengiriman dan pengisian air telah selesai.',
                'completed_at' => now(),
            ]);

            // 2. Update assignment status
            $assignment->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);

            // 3. Update order status
            $order = $assignment->order;
            $order->update([
                'status' => 'Selesai',
                'payment_status' => 'Paid',
            ]);

            // 4. Release vehicle & driver
            $assignment->vehicle->update(['status' => 'Ready']);
            $assignment->driver->update(['availability_status' => 'Aktif']);

            // 5. Automatic financial income record (guaranteed idempotent - no duplicate)
            $existingIncome = FinancialTransaction::where('order_id', $order->id)->where('type', 'income')->first();
            if (!$existingIncome) {
                FinancialTransaction::create([
                    'order_id' => $order->id,
                    'type' => 'income',
                    'category' => 'Penjualan Air',
                    'amount' => $order->total_price,
                    'transaction_date' => Carbon::today(),
                    'description' => "Pemasukan otomatis dari pesanan selesai {$order->order_number} ({$order->volume} L ke {$order->customer->company_name})",
                    'created_by' => Auth::id(),
                ]);
            }
        });

        // Notifications
        NotificationService::notifyCustomer(
            $assignment->order,
            'Pengiriman Air Selesai!',
            "Pesanan {$assignment->order->order_number} telah berhasil dikirim. Silakan berikan penilaian dan ulasan layanan Anda.",
            route('customer.orders.show', $assignment->order_id),
            'success'
        );

        NotificationService::notifyAdmins(
            'Pengiriman Selesai',
            "Driver {$assignment->driver->user->name} telah menyelesaikan pengiriman order {$assignment->order->order_number}. Pemasukan Rp " . number_format($assignment->order->total_price, 0, ',', '.') . " telah dicatat otomatis.",
            route('admin.orders.show', $assignment->order_id),
            'success',
            $assignment->order_id
        );

        ActivityLog::log('ORDER_COMPLETED', "Order {$assignment->order->order_number} diselesaikan oleh Driver {$assignment->driver->user->name}. Bukti terunggah.", Auth::id());

        return redirect()->route('driver.jobs.show', $assignment->id)
            ->with('success', 'Pengiriman berhasil diselesaikan! Bukti pengiriman telah tersimpan.');
    }

    public function reportIssue(Request $request, Assignment $assignment)
    {
        $this->authorizeDriver($assignment);

        $validated = $request->validate([
            'issue_type' => ['required', 'string'],
            'description' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $reason = "[{$validated['issue_type']}] " . $validated['description'];

        DB::transaction(function () use ($assignment, $reason, $validated) {
            $assignment->update([
                'status' => 'Cancelled',
                'driver_issue_reason' => $reason,
            ]);

            // Set order to Pending Review (NOT deleted)
            $assignment->order->update([
                'status' => 'Pending Review',
                'pending_reason' => "Driver melaporkan kendala di lapangan: {$reason}",
            ]);

            // If issue is vehicle breakdown, mark vehicle as Maintenance
            if (in_array($validated['issue_type'], ['Kendaraan Rusak', 'Ban Bocor', 'Kendala Teknis'])) {
                $assignment->vehicle->update([
                    'status' => 'Maintenance',
                    'notes' => "Laporan kendala driver: {$reason}",
                ]);
            }

            $assignment->driver->update(['availability_status' => 'Aktif']);
        });

        NotificationService::notifyAdmins(
            'Laporan Kendala Driver!',
            "Driver {$assignment->driver->user->name} melaporkan kendala pada order {$assignment->order->order_number}: {$reason}. Mohon tinjau di dashboard.",
            route('admin.orders.show', $assignment->order_id),
            'danger',
            $assignment->order_id
        );

        NotificationService::notifyCustomer(
            $assignment->order,
            'Pemberitahuan Kendala Pengiriman',
            "Terdapat kendala teknis dalam pengiriman pesanan Anda: {$reason}. Tim operasional kami sedang menangani penugasan armada pengganti.",
            route('customer.orders.show', $assignment->order_id),
            'warning'
        );

        ActivityLog::log('DRIVER_ISSUE_REPORTED', "Kendala dilaporkan untuk order {$assignment->order->order_number}: {$reason}", Auth::id());

        return redirect()->route('driver.dashboard')
            ->with('warning', 'Laporan kendala telah dikirimkan ke Admin. Status pesanan berubah menjadi Pending Review.');
    }

    protected function authorizeDriver(Assignment $assignment): void
    {
        $driver = Auth::user()->driver;
        if (!$driver || $assignment->driver_id !== $driver->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk pekerjaan ini.');
        }
    }
}

