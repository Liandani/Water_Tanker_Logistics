<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Driver;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DriverManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::with(['user', 'ratings'])->latest();

        if ($request->filled('availability')) {
            $query->where('availability_status', $request->availability);
        }

        if ($request->filled('verification')) {
            $query->where('verification_status', $request->verification);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('license_number', 'like', "%{$search}%")
                  ->orWhere('identity_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $drivers = $query->paginate(10)->withQueryString();

        return view('admin.drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('admin.drivers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'license_number' => ['nullable', 'string', 'max:50'],
            'identity_number' => ['nullable', 'string', 'max:50'],
            'availability_status' => ['required', 'in:Aktif,Libur,On Delivery'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'driver',
                'status' => 'active',
            ]);

            Driver::create([
                'user_id' => $user->id,
                'license_number' => $validated['license_number'] ?? null,
                'identity_number' => $validated['identity_number'] ?? null,
                'availability_status' => $validated['availability_status'],
                'verification_status' => 'verified', // Directly verified by admin
                'current_latitude' => -6.2088000,
                'current_longitude' => 106.8456000,
            ]);

            ActivityLog::log('DRIVER_CREATED_ADMIN', "Admin menambahkan data driver baru {$validated['name']}.");
        });

        return redirect()->route('admin.drivers.index')->with('success', 'Driver baru berhasil ditambahkan.');
    }

    public function show(Driver $driver)
    {
        $driver->load(['user', 'assignments.order.customer', 'assignments.vehicle', 'ratings.customer']);
        return view('admin.drivers.show', compact('driver'));
    }

    public function edit(Driver $driver)
    {
        $driver->load('user');
        return view('admin.drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $driver->user_id],
            'phone' => ['required', 'string', 'max:20'],
            'license_number' => ['nullable', 'string', 'max:50'],
            'identity_number' => ['nullable', 'string', 'max:50'],
            'availability_status' => ['required', 'in:Aktif,Libur,On Delivery'],
            'verification_status' => ['required', 'in:pending,verified,rejected'],
            'status' => ['required', 'in:active,inactive,pending'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($driver, $validated) {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'status' => $validated['status'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $driver->user->update($userData);

            $driver->update([
                'license_number' => $validated['license_number'] ?? $driver->license_number,
                'identity_number' => $validated['identity_number'] ?? $driver->identity_number,
                'availability_status' => $validated['availability_status'],
                'verification_status' => $validated['verification_status'],
            ]);

            ActivityLog::log('DRIVER_UPDATED_ADMIN', "Admin memperbarui data driver {$driver->user->name}.");
        });

        return redirect()->route('admin.drivers.index')->with('success', 'Data driver berhasil diperbarui.');
    }

    public function verifyDriver(Request $request, Driver $driver)
    {
        $status = $request->input('action') === 'approve' ? 'verified' : 'rejected';
        $userStatus = $status === 'verified' ? 'active' : 'inactive';

        DB::transaction(function () use ($driver, $status, $userStatus) {
            $driver->update([
                'verification_status' => $status,
                'availability_status' => $status === 'verified' ? 'Aktif' : 'Libur',
            ]);
            $driver->user->update(['status' => $userStatus]);

            ActivityLog::log('DRIVER_VERIFICATION', "Admin {$status} akun driver {$driver->user->name}.");
        });

        NotificationService::notifyDriver(
            $driver->user,
            $status === 'verified' ? 'Akun Driver Diverifikasi!' : 'Registrasi Driver Ditolak',
            $status === 'verified' ? 'Selamat! Akun Anda telah diverifikasi oleh Admin. Anda sekarang dapat menerima pekerjaan pengiriman.' : 'Mohon maaf, permohonan pendaftaran driver Anda belum dapat disetujui.',
            route('driver.dashboard'),
            $status === 'verified' ? 'success' : 'danger'
        );

        return back()->with('success', "Driver {$driver->user->name} berhasil di-{$status}.");
    }

    public function destroy(Driver $driver)
    {
        $name = $driver->user->name;
        $user = $driver->user;

        DB::transaction(function () use ($driver, $user) {
            $driver->delete();
            $user->delete();
        });

        ActivityLog::log('DRIVER_DELETED_ADMIN', "Admin menghapus data driver {$name}.");

        return redirect()->route('admin.drivers.index')->with('success', "Driver {$name} berhasil dihapus.");
    }
}

