<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Rating;
use App\Models\TariffSetting;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $customer = Auth::user()->customer;
        $query = Order::where('customer_id', $customer->id)
            ->with(['activeAssignment.driver.user', 'activeAssignment.vehicle', 'rating'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('delivery_address', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('customer.orders.index', compact('orders'));
    }

    public function create()
    {
        $customer = Auth::user()->customer;
        $tariff = TariffSetting::current();

        return view('customer.orders.create', compact('customer', 'tariff'));
    }

    public function calculateApi(Request $request)
    {
        $request->validate([
            'volume' => ['required', 'integer', 'min:500'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $tariff = TariffSetting::current();
        $volume = (int) $request->volume;
        $destLat = (float) $request->latitude;
        $destLng = (float) $request->longitude;

        $distance = Order::calculateHaversineDistance(
            (float) $tariff->depot_latitude,
            (float) $tariff->depot_longitude,
            $destLat,
            $destLng
        );

        $fleetInfo = Order::determineFleetCategory($volume);

        $waterPrice = $volume * (float) $tariff->water_price_per_liter;
        $deliveryPrice = ((float) $tariff->base_fee) + ($distance * (float) $tariff->delivery_price_per_km * $fleetInfo['trips']);
        $totalPrice = $waterPrice + $deliveryPrice;

        return response()->json([
            'success' => true,
            'volume' => $volume,
            'distance_km' => $distance,
            'fleet_recommendation' => $fleetInfo['category'],
            'trips_count' => $fleetInfo['trips'],
            'water_price_rate' => (float) $tariff->water_price_per_liter,
            'delivery_price_rate' => (float) $tariff->delivery_price_per_km,
            'base_fee' => (float) $tariff->base_fee,
            'water_price' => $waterPrice,
            'delivery_price' => $deliveryPrice,
            'total_price' => $totalPrice,
            'formatted' => [
                'water_price' => 'Rp ' . number_format($waterPrice, 0, ',', '.'),
                'delivery_price' => 'Rp ' . number_format($deliveryPrice, 0, ',', '.'),
                'total_price' => 'Rp ' . number_format($totalPrice, 0, ',', '.'),
                'distance' => number_format($distance, 1, ',', '.') . ' km',
            ]
        ]);
    }

    public function store(Request $request)
    {
        $customer = Auth::user()->customer;
        $tariff = TariffSetting::current();

        $validated = $request->validate([
            'volume' => ['required', 'integer', 'min:500'],
            'delivery_address' => ['required', 'string'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'delivery_date' => ['required', 'date', 'after_or_equal:today'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_phone' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ]);

        $volume = (int) $validated['volume'];
        $distance = Order::calculateHaversineDistance(
            (float) $tariff->depot_latitude,
            (float) $tariff->depot_longitude,
            (float) $validated['latitude'],
            (float) $validated['longitude']
        );

        $fleetInfo = Order::determineFleetCategory($volume);

        $waterPrice = $volume * (float) $tariff->water_price_per_liter;
        $deliveryPrice = ((float) $tariff->base_fee) + ($distance * (float) $tariff->delivery_price_per_km * $fleetInfo['trips']);
        $totalPrice = $waterPrice + $deliveryPrice;

        $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_id' => $customer->id,
            'volume' => $volume,
            'fleet_category_recommendation' => $fleetInfo['category'],
            'trips_count' => $fleetInfo['trips'],
            'delivery_address' => $validated['delivery_address'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'delivery_date' => $validated['delivery_date'],
            'recipient_name' => $validated['recipient_name'] ?? $customer->contact_person,
            'recipient_phone' => $validated['recipient_phone'] ?? Auth::user()->phone,
            'notes' => $validated['notes'],
            'estimated_distance' => $distance,
            'water_price_rate' => $tariff->water_price_per_liter,
            'delivery_price_rate' => $tariff->delivery_price_per_km,
            'water_price' => $waterPrice,
            'delivery_price' => $deliveryPrice,
            'total_price' => $totalPrice,
            'status' => 'Pending',
            'payment_status' => 'Unpaid',
        ]);

        NotificationService::notifyAdmins(
            'Pesanan Air Baru Masuk',
            "Pesanan {$order->order_number} ({$order->volume} L) dibuat oleh {$customer->company_name}.",
            route('admin.orders.show', $order->id),
            'info',
            $order->id
        );

        NotificationService::notifyCustomer(
            $order,
            'Pesanan Berhasil Dibuat',
            "Pesanan {$order->order_number} berhasil dibuat dan sedang menunggu validasi admin.",
            route('customer.orders.show', $order->id),
            'success'
        );

        ActivityLog::log('ORDER_CREATED', "Pesanan {$order->order_number} dibuat oleh {$customer->company_name}.", Auth::id());

        return redirect()->route('customer.orders.show', $order->id)
            ->with('success', 'Pesanan air berhasil dibuat dan sedang menunggu verifikasi Admin.');
    }

    public function show(Order $order)
    {
        $this->authorizeCustomer($order);

        $order->load([
            'customer.user',
            'activeAssignment.driver.user',
            'activeAssignment.vehicle',
            'activeAssignment.proof',
            'document',
            'rating'
        ]);

        return view('customer.orders.show', compact('order'));
    }

    public function tracking(Order $order)
    {
        $this->authorizeCustomer($order);

        $order->load([
            'customer',
            'activeAssignment.driver.user',
            'activeAssignment.vehicle',
            'activeAssignment.latestLocation',
            'activeAssignment.proof'
        ]);

        $tariff = TariffSetting::current();

        return view('customer.orders.tracking', compact('order', 'tariff'));
    }

    public function trackingLocationApi(Order $order)
    {
        $this->authorizeCustomer($order);

        if ($order->status !== 'Dikirim') {
            return response()->json([
                'live_tracking_active' => false,
                'status' => $order->status,
                'message' => 'Tracking GPS hanya aktif ketika armada berstatus Dikirim.',
            ]);
        }

        $assignment = $order->activeAssignment;
        if (!$assignment || !$assignment->driver) {
            return response()->json([
                'live_tracking_active' => false,
                'message' => 'Penugasan armada belum aktif.',
            ]);
        }

        $latestLocation = $assignment->latestLocation;
        $driverLat = $latestLocation ? (float) $latestLocation->latitude : (float) $assignment->driver->current_latitude;
        $driverLng = $latestLocation ? (float) $latestLocation->longitude : (float) $assignment->driver->current_longitude;

        return response()->json([
            'live_tracking_active' => true,
            'status' => $order->status,
            'driver_name' => $assignment->driver->user->name,
            'driver_phone' => $assignment->driver->user->phone,
            'plate_number' => $assignment->vehicle->plate_number,
            'vehicle_type' => $assignment->vehicle->vehicle_type,
            'driver_latitude' => $driverLat,
            'driver_longitude' => $driverLng,
            'dest_latitude' => (float) $order->latitude,
            'dest_longitude' => (float) $order->longitude,
            'dest_address' => $order->delivery_address,
            'last_update' => $latestLocation ? $latestLocation->recorded_at->diffForHumans() : 'Baru saja',
        ]);
    }

    public function invoice(Order $order)
    {
        $this->authorizeCustomer($order);

        $order->load(['customer.user', 'activeAssignment.driver.user', 'activeAssignment.vehicle', 'document']);
        $tariff = TariffSetting::current();

        return view('customer.orders.invoice', compact('order', 'tariff'));
    }

    public function rate(Request $request, Order $order)
    {
        $this->authorizeCustomer($order);

        if ($order->status !== 'Selesai') {
            return back()->with('error', 'Rating hanya dapat diberikan pada pesanan yang telah selesai.');
        }

        if ($order->rating) {
            return back()->with('error', 'Anda telah memberikan rating untuk pesanan ini.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        $assignment = $order->activeAssignment;
        if (!$assignment || !$assignment->driver_id) {
            return back()->with('error', 'Driver tidak ditemukan untuk pesanan ini.');
        }

        Rating::create([
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'driver_id' => $assignment->driver_id,
            'rating' => $validated['rating'],
            'review' => $validated['review'],
        ]);

        NotificationService::notifyDriver(
            $assignment->driver->user,
            'Penilaian Pelanggan Diterima',
            "Pelanggan {$order->customer->company_name} memberikan rating {$validated['rating']} bintang untuk pesanan {$order->order_number}.",
            route('driver.jobs.show', $assignment->id),
            'success',
            $order->id
        );

        ActivityLog::log('RATING_SUBMITTED', "Rating {$validated['rating']} bintang untuk driver {$assignment->driver->user->name} pada pesanan {$order->order_number}.", Auth::id());

        return back()->with('success', 'Terima kasih! Ulasan dan rating Anda berhasil disimpan.');
    }

    protected function authorizeCustomer(Order $order): void
    {
        $customer = Auth::user()->customer;
        if (!$customer || $order->customer_id !== $customer->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk pesanan ini.');
        }
    }
}

