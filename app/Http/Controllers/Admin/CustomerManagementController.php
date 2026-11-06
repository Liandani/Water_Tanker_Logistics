<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('user')->withCount('orders')->latest();

        if ($request->filled('category')) {
            $query->where('business_category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $customers = $query->paginate(10)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'business_category' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'customer',
                'status' => 'active',
            ]);

            Customer::create([
                'user_id' => $user->id,
                'company_name' => $validated['company_name'],
                'contact_person' => $validated['name'],
                'address' => $validated['address'],
                'latitude' => $validated['latitude'] ?? -6.200000,
                'longitude' => $validated['longitude'] ?? 106.816666,
                'business_category' => $validated['business_category'],
            ]);

            ActivityLog::log('CUSTOMER_CREATED_ADMIN', "Admin membuat akun pelanggan baru {$validated['company_name']}.");
        });

        return redirect()->route('admin.customers.index')->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['user', 'orders' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $customer->load('user');
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $customer->user_id],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'business_category' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($customer, $validated) {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $customer->user->update($userData);

            $customer->update([
                'company_name' => $validated['company_name'],
                'contact_person' => $validated['name'],
                'address' => $validated['address'],
                'business_category' => $validated['business_category'],
                'latitude' => $validated['latitude'] ?? $customer->latitude,
                'longitude' => $validated['longitude'] ?? $customer->longitude,
            ]);

            ActivityLog::log('CUSTOMER_UPDATED_ADMIN', "Admin memperbarui data pelanggan {$customer->company_name}.");
        });

        return redirect()->route('admin.customers.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $name = $customer->company_name;
        $user = $customer->user;

        DB::transaction(function () use ($customer, $user) {
            $customer->delete();
            $user->delete();
        });

        ActivityLog::log('CUSTOMER_DELETED_ADMIN', "Admin menghapus pelanggan {$name}.");

        return redirect()->route('admin.customers.index')->with('success', "Pelanggan {$name} berhasil dihapus.");
    }
}

