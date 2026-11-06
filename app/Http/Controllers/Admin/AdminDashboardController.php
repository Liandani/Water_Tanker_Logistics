<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Driver;
use App\Models\FinancialTransaction;
use App\Models\Order;
use App\Models\TariffSetting;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Order metrics
        $todayOrders = Order::whereDate('created_at', today())->count();
        $pendingOrders = Order::where('status', 'Pending')->count();
        $processingOrders = Order::where('status', 'Diproses')->count();
        $inTransitOrders = Order::where('status', 'Dikirim')->count();
        $completedOrders = Order::where('status', 'Selesai')->count();
        $pendingReviewOrders = Order::where('status', 'Pending Review')->count();

        // 2. Financial metrics this month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $incomeThisMonth = (float) FinancialTransaction::where('type', 'income')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $expenseThisMonth = (float) FinancialTransaction::where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $netProfitThisMonth = $incomeThisMonth - $expenseThisMonth;

        // 3. Fleet & Driver metrics
        $activeDrivers = Driver::where('availability_status', 'Aktif')->where('verification_status', 'verified')->count();
        $readyVehicles = Vehicle::where('status', 'Ready')->count();
        $onDutyVehicles = Vehicle::where('status', 'On Duty')->count();
        $maintenanceVehicles = Vehicle::where('status', 'Maintenance')->count();

        // 4. Chart data: Monthly Income & Expense for last 6 months
        $months = [];
        $incomeData = [];
        $expenseData = [];
        $ordersCountData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $mStart = $monthDate->copy()->startOfMonth();
            $mEnd = $monthDate->copy()->endOfMonth();
            $label = $monthDate->translatedFormat('M Y');
            $months[] = $label;

            $mIncome = (float) FinancialTransaction::where('type', 'income')
                ->whereBetween('transaction_date', [$mStart, $mEnd])
                ->sum('amount');
            $mExpense = (float) FinancialTransaction::where('type', 'expense')
                ->whereBetween('transaction_date', [$mStart, $mEnd])
                ->sum('amount');
            $mOrders = Order::whereBetween('created_at', [$mStart, $mEnd])->count();

            $incomeData[] = $mIncome;
            $expenseData[] = $mExpense;
            $ordersCountData[] = $mOrders;
        }

        // Recent orders
        $recentOrders = Order::with(['customer', 'activeAssignment.driver.user', 'activeAssignment.vehicle'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'todayOrders',
            'pendingOrders',
            'processingOrders',
            'inTransitOrders',
            'completedOrders',
            'pendingReviewOrders',
            'incomeThisMonth',
            'expenseThisMonth',
            'netProfitThisMonth',
            'activeDrivers',
            'readyVehicles',
            'onDutyVehicles',
            'maintenanceVehicles',
            'months',
            'incomeData',
            'expenseData',
            'ordersCountData',
            'recentOrders'
        ));
    }

    public function monitoringMaps()
    {
        $tariff = TariffSetting::current();
        return view('admin.monitoring.maps', compact('tariff'));
    }

    public function monitoringData()
    {
        $vehicles = Vehicle::with(['currentAssignment.driver.user', 'currentAssignment.order.customer'])
            ->get()
            ->map(function ($v) {
                $assignment = $v->currentAssignment;
                $driverName = $assignment && $assignment->driver ? $assignment->driver->user->name : '-';
                $orderNumber = $assignment && $assignment->order ? $assignment->order->order_number : '-';
                $customerName = $assignment && $assignment->order ? $assignment->order->customer->company_name : '-';
                $destination = $assignment && $assignment->order ? $assignment->order->delivery_address : '-';
                $volume = $assignment && $assignment->order ? $assignment->order->volume : 0;

                // Color coding rules:
                // Hijau: Ready
                // Biru: Sedang Dikirim
                // Merah: Maintenance
                // Kuning: Pending / Kendala
                $markerColor = match ($v->status) {
                    'Ready' => 'green',
                    'On Duty' => 'blue',
                    'Maintenance' => 'red',
                    default => 'gray',
                };

                if ($assignment && $assignment->order && $assignment->order->status === 'Pending Review') {
                    $markerColor = 'yellow';
                }

                // If coordinates not set, default to company depot or offset slightly
                $lat = $v->current_latitude ?? -6.2088000;
                $lng = $v->current_longitude ?? 106.8456000;

                return [
                    'id' => $v->id,
                    'plate_number' => $v->plate_number,
                    'vehicle_type' => $v->vehicle_type,
                    'capacity' => $v->capacity,
                    'status' => $v->status,
                    'marker_color' => $markerColor,
                    'driver_name' => $driverName,
                    'order_number' => $orderNumber,
                    'customer_name' => $customerName,
                    'destination' => $destination,
                    'volume' => $volume,
                    'latitude' => (float) $lat,
                    'longitude' => (float) $lng,
                    'updated_at' => $v->updated_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'vehicles' => $vehicles,
        ]);
    }
}

