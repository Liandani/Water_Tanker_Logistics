<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $driver = $user->driver;

        if (!$driver) {
            return redirect()->route('login')->with('error', 'Profil driver tidak ditemukan.');
        }

        if ($driver->verification_status === 'pending') {
            return redirect()->route('driver.pending');
        }

        // Active / ongoing task (Assigned, Accepted, In Transit, Filling Water)
        $activeJob = Assignment::where('driver_id', $driver->id)
            ->whereIn('status', ['Assigned', 'Accepted', 'In Transit', 'Filling Water'])
            ->with(['order.customer.user', 'vehicle', 'document'])
            ->latest()
            ->first();

        // Tasks today count
        $tasksToday = Assignment::where('driver_id', $driver->id)
            ->whereDate('assigned_at', today())
            ->count();

        // Total completed deliveries
        $completedTotal = Assignment::where('driver_id', $driver->id)
            ->where('status', 'Completed')
            ->count();

        // Recent history
        $recentJobs = Assignment::where('driver_id', $driver->id)
            ->with(['order.customer', 'vehicle', 'proof'])
            ->latest()
            ->take(5)
            ->get();

        return view('driver.dashboard', compact(
            'driver',
            'activeJob',
            'tasksToday',
            'completedTotal',
            'recentJobs'
        ));
    }

    public function toggleAvailability(Request $request)
    {
        $driver = Auth::user()->driver;

        // If driver is currently on delivery, cannot toggle to Libur
        $hasActiveJob = Assignment::where('driver_id', $driver->id)
            ->whereIn('status', ['Accepted', 'In Transit', 'Filling Water'])
            ->exists();

        if ($hasActiveJob) {
            return back()->with('error', 'Anda tidak dapat mengubah status menjadi Libur saat sedang menjalankan tugas pengiriman.');
        }

        $newStatus = $driver->availability_status === 'Aktif' ? 'Libur' : 'Aktif';
        $driver->update(['availability_status' => $newStatus]);

        ActivityLog::log('DRIVER_STATUS_CHANGE', "Driver {$driver->user->name} mengubah status kerja menjadi {$newStatus}.", Auth::id());

        return back()->with('success', "Status kerja berhasil diubah menjadi: {$newStatus}");
    }

    public function profile()
    {
        $driver = Auth::user()->driver;
        return view('driver.profile', compact('driver'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $driver = $user->driver;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'license_number' => ['nullable', 'string', 'max:50'],
            'identity_number' => ['nullable', 'string', 'max:50'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        $driver->update([
            'license_number' => $validated['license_number'] ?? $driver->license_number,
            'identity_number' => $validated['identity_number'] ?? $driver->identity_number,
        ]);

        return back()->with('success', 'Profil driver berhasil diperbarui.');
    }
}

