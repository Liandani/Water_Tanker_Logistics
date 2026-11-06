<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with('currentAssignment.driver.user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('capacity')) {
            $query->where('capacity', $request->capacity);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('brand_model', 'like', "%{$search}%")
                  ->orWhere('vehicle_type', 'like', "%{$search}%");
            });
        }

        $vehicles = $query->paginate(10)->withQueryString();

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => ['required', 'string', 'max:20', 'unique:vehicles'],
            'capacity' => ['required', 'integer', 'min:1000'],
            'vehicle_type' => ['required', 'string'],
            'brand_model' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Ready,On Duty,Maintenance'],
            'notes' => ['nullable', 'string'],
        ]);

        $vehicle = Vehicle::create([
            'plate_number' => strtoupper(trim($validated['plate_number'])),
            'capacity' => $validated['capacity'],
            'vehicle_type' => $validated['vehicle_type'],
            'brand_model' => $validated['brand_model'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'current_latitude' => -6.2088000,
            'current_longitude' => 106.8456000,
        ]);

        ActivityLog::log('VEHICLE_CREATED_ADMIN', "Admin menambahkan armada baru {$vehicle->plate_number} ({$vehicle->vehicle_type}).");

        return redirect()->route('admin.vehicles.index')->with('success', "Armada {$vehicle->plate_number} berhasil ditambahkan.");
    }

    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number' => ['required', 'string', 'max:20', 'unique:vehicles,plate_number,' . $vehicle->id],
            'capacity' => ['required', 'integer', 'min:1000'],
            'vehicle_type' => ['required', 'string'],
            'brand_model' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Ready,On Duty,Maintenance'],
            'notes' => ['nullable', 'string'],
        ]);

        $vehicle->update([
            'plate_number' => strtoupper(trim($validated['plate_number'])),
            'capacity' => $validated['capacity'],
            'vehicle_type' => $validated['vehicle_type'],
            'brand_model' => $validated['brand_model'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        ActivityLog::log('VEHICLE_UPDATED_ADMIN', "Admin memperbarui data armada {$vehicle->plate_number}.");

        return redirect()->route('admin.vehicles.index')->with('success', "Armada {$vehicle->plate_number} berhasil diperbarui.");
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->status === 'On Duty') {
            return back()->with('error', 'Tidak dapat menghapus armada yang sedang bertugas (On Duty).');
        }

        $plate = $vehicle->plate_number;
        $vehicle->delete();

        ActivityLog::log('VEHICLE_DELETED_ADMIN', "Admin menghapus data armada {$plate}.");

        return redirect()->route('admin.vehicles.index')->with('success', "Armada {$plate} berhasil dihapus.");
    }
}

