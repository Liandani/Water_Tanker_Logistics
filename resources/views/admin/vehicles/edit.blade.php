@extends('layouts.admin')

@section('title', 'Edit Armada ' . $vehicle->plate_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-3">
            <a href="{{ route('admin.vehicles.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left"></i> Kembali ke Data Armada
            </a>
            <h4 class="fw-bold text-dark-blue mt-1">Ubah Data Armada {{ $vehicle->plate_number }}</h4>
        </div>

        <div class="card card-fresh p-4 p-md-5">
            <form action="{{ route('admin.vehicles.update', $vehicle->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="plate_number" class="form-label small fw-bold text-dark">Nomor Plat Polisi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase @error('plate_number') is-invalid @enderror" id="plate_number" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" required>
                        @error('plate_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="brand_model" class="form-label small fw-bold text-dark">Merek & Model Kendaraan</label>
                        <input type="text" class="form-control" id="brand_model" name="brand_model" value="{{ old('brand_model', $vehicle->brand_model) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="vehicle_type" class="form-label small fw-bold text-dark">Kategori Armada Tangki <span class="text-danger">*</span></label>
                        <select id="vehicle_type" class="form-select @error('vehicle_type') is-invalid @enderror" name="vehicle_type" required>
                            <option value="Tangki 7.000 L" {{ old('vehicle_type', $vehicle->vehicle_type) === 'Tangki 7.000 L' ? 'selected' : '' }}>Tangki 7.000 L</option>
                            <option value="Tangki 8.000 L" {{ old('vehicle_type', $vehicle->vehicle_type) === 'Tangki 8.000 L' ? 'selected' : '' }}>Tangki 8.000 L</option>
                            <option value="Tangki 16.000 L" {{ old('vehicle_type', $vehicle->vehicle_type) === 'Tangki 16.000 L' ? 'selected' : '' }}>Tangki 16.000 L</option>
                            <option value="Tangki 20.000 L" {{ old('vehicle_type', $vehicle->vehicle_type) === 'Tangki 20.000 L' ? 'selected' : '' }}>Tangki 20.000 L</option>
                            <option value="XXL / Multiple Trip" {{ old('vehicle_type', $vehicle->vehicle_type) === 'XXL / Multiple Trip' ? 'selected' : '' }}>XXL / Multiple Trip</option>
                        </select>
                        @error('vehicle_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="capacity" class="form-label small fw-bold text-dark">Kapasitas Maksimal (Liter) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity', $vehicle->capacity) }}" required min="1000" step="500">
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="status" class="form-label small fw-bold text-dark">Status Operasional <span class="text-danger">*</span></label>
                        <select id="status" class="form-select @error('status') is-invalid @enderror" name="status" required>
                            <option value="Ready" {{ old('status', $vehicle->status) === 'Ready' ? 'selected' : '' }}>Ready (Siap Ditugaskan)</option>
                            <option value="On Duty" {{ old('status', $vehicle->status) === 'On Duty' ? 'selected' : '' }}>On Duty (Sedang Bertugas)</option>
                            <option value="Maintenance" {{ old('status', $vehicle->status) === 'Maintenance' ? 'selected' : '' }}>Maintenance (Servis / Perbaikan)</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label small fw-bold text-dark">Catatan Spesifikasi / Kondisi Tangki</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $vehicle->notes) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('admin.vehicles.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary-blue fw-bold px-4">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

