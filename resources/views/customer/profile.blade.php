@extends('layouts.customer')

@section('title', 'Profil Perusahaan Mitra')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card card-fresh p-4 p-md-5">
            <h4 class="fw-bold text-dark-blue mb-1">Profil Perusahaan Mitra</h4>
            <p class="text-muted small mb-4">Perbarui informasi identitas perusahaan dan penanggung jawab pengiriman</p>

            <form action="{{ route('customer.profile.update') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="company_name" class="form-label fw-semibold small text-dark">Nama Perusahaan / Mitra <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name', $customer->company_name) }}" required>
                    @error('company_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold small text-dark">Nama Penanggung Jawab (PIC) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold small text-dark">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small text-dark">Alamat Email (Akun)</label>
                    <input type="email" class="form-control bg-light" value="{{ auth()->user()->email }}" readonly>
                    <small class="text-muted" style="font-size: 0.72rem;">Email digunakan sebagai identitas akun login.</small>
                </div>

                <div class="mb-3">
                    <label for="business_category" class="form-label fw-semibold small text-dark">Kategori Bisnis <span class="text-danger">*</span></label>
                    <select id="business_category" class="form-select @error('business_category') is-invalid @enderror" name="business_category" required>
                        <option value="Hotel" {{ old('business_category', $customer->business_category) === 'Hotel' ? 'selected' : '' }}>Hotel & Penginapan</option>
                        <option value="Proyek Konstruksi" {{ old('business_category', $customer->business_category) === 'Proyek Konstruksi' ? 'selected' : '' }}>Proyek Konstruksi & Sipil</option>
                        <option value="Pabrik/Industri" {{ old('business_category', $customer->business_category) === 'Pabrik/Industri' ? 'selected' : '' }}>Pabrik / Kawasan Industri</option>
                        <option value="Restoran" {{ old('business_category', $customer->business_category) === 'Restoran' ? 'selected' : '' }}>Restoran & Food Beverage</option>
                        <option value="Lainnya" {{ old('business_category', $customer->business_category) === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('business_category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="address" class="form-label fw-semibold small text-dark">Alamat Baku Pengiriman <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address', $customer->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary-blue fw-bold px-4">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

