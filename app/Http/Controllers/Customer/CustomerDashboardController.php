<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $customer = $user->customer;

        if (!$customer) {
            return redirect()->route('login')->with('error', 'Profil pelanggan tidak ditemukan.');
        }

        $totalOrders = Order::where('customer_id', $customer->id)->count();
        $processingOrders = Order::where('customer_id', $customer->id)->where('status', 'Diproses')->count();
        $inTransitOrders = Order::where('customer_id', $customer->id)->where('status', 'Dikirim')->count();
        $completedOrders = Order::where('customer_id', $customer->id)->where('status', 'Selesai')->count();

        $recentOrders = Order::where('customer_id', $customer->id)
            ->with(['activeAssignment.driver.user', 'activeAssignment.vehicle'])
            ->latest()
            ->take(6)
            ->get();

        return view('customer.dashboard', compact(
            'customer',
            'totalOrders',
            'processingOrders',
            'inTransitOrders',
            'completedOrders',
            'recentOrders'
        ));
    }

    public function profile()
    {
        $customer = Auth::user()->customer;
        return view('customer.profile', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $customer = $user->customer;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'business_category' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        $customer->update([
            'company_name' => $validated['company_name'],
            'contact_person' => $validated['name'],
            'address' => $validated['address'],
            'business_category' => $validated['business_category'],
            'latitude' => $validated['latitude'] ?? $customer->latitude,
            'longitude' => $validated['longitude'] ?? $customer->longitude,
        ]);

        return back()->with('success', 'Profil perusahaan berhasil diperbarui.');
    }
}

