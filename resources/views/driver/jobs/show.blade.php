@extends('layouts.driver')

@section('title', 'Detail Tugas ' . $assignment->order->order_number)

@section('content')
<div class="mb-3">
    <a href="{{ route('driver.jobs.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Tugas
    </a>
    <div class="d-flex justify-content-between align-items-center mt-1">
        <h5 class="fw-bold text-dark-blue mb-0">Tugas #{{ $assignment->order->order_number }}</h5>
        <span class="badge {{ $assignment->status === 'Completed' ? 'badge-selesai' : ($assignment->status === 'In Transit' ? 'badge-dikirim' : ($assignment->status === 'Assigned' ? 'badge-pending' : 'badge-diproses')) }} px-3 py-1">
            {{ $assignment->status }}
        </span>
    </div>
</div>

<!-- Primary Action Panel depending on assignment status -->
@if($assignment->status === 'Assigned')
    <!-- Accept or Reject New Job -->
    <div class="card driver-card p-3 mb-3 bg-light border-warning shadow-sm">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-bell-fill text-warning fs-4"></i>
            <div>
                <strong class="text-dark small">Tugas Pengiriman Baru!</strong>
                <div class="text-muted" style="font-size: 0.72rem;">Harap konfirmasi kesiapan Anda menerima tugas ini.</div>
            </div>
        </div>
        <div class="row g-2 mt-1">
            <div class="col-7">
                <form action="{{ route('driver.jobs.accept', $assignment->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i> Terima Job
                    </button>
                </form>
            </div>
            <div class="col-5">
                <button type="button" class="btn btn-outline-danger w-100 fw-bold py-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle me-1"></i> Tolak
                </button>
            </div>
        </div>
    </div>
@elseif($assignment->status === 'Accepted')
    <!-- Ready to Depart -->
    <div class="card driver-card p-3 mb-3 bg-primary-blue text-white shadow">
        <h6 class="fw-bold mb-1">Siap Berangkat ke Lokasi Pengiriman?</h6>
        <p class="small text-light opacity-90 mb-3">Tekan tombol di bawah untuk mengubah status order menjadi <strong>DIKIRIM</strong> dan mengaktifkan live tracking bagi pelanggan.</p>
        <form action="{{ route('driver.jobs.status', $assignment->id) }}" method="POST">
            @csrf
            <input type="hidden" name="action" value="start_transit">
            <button type="submit" class="btn btn-warning w-100 fw-bold py-3 fs-6 shadow">
                <i class="bi bi-truck me-2"></i> Mulai Perjalanan (Menuju Lokasi)
            </button>
        </form>
    </div>
@elseif($assignment->status === 'In Transit')
    <!-- In Transit: Arrived at Location -->
    <div class="card driver-card p-3 mb-3 bg-light-blue text-dark-blue shadow-sm border-primary">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-primary text-white fw-bold">SEDANG MENUJU LOKASI</span>
            <small class="text-muted"><i class="bi bi-clock me-1"></i> Berangkat: {{ $assignment->started_at ? $assignment->started_at->format('H:i') : '-' }}</small>
        </div>
        <p class="small text-muted mb-3">Jika armada Anda telah tiba di gerbang atau bak penampungan pelanggan, tekan tombol berikut untuk memulai pengisian air.</p>
        <div class="d-grid gap-2">
            <form action="{{ route('driver.jobs.status', $assignment->id) }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="start_filling">
                <button type="submit" class="btn btn-primary-blue w-100 fw-bold py-3 fs-6 shadow">
                    <i class="bi bi-water me-2"></i> Tiba di Lokasi - Mulai Pengisian Air
                </button>
            </form>
            <a href="{{ route('driver.jobs.navigation', $assignment->id) }}" class="btn btn-outline-primary btn-sm py-2">
                <i class="bi bi-compass-fill me-1"></i> Buka Peta Rute Navigasi
            </a>
        </div>
    </div>
@elseif($assignment->status === 'Filling Water')
    <!-- Filling Water: Ready to Complete with Proof -->
    <div class="card driver-card p-3 mb-3 bg-success-subtle text-dark border-success shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-info text-dark fw-bold">PROSES PENGISIAN AIR</span>
            <small class="text-muted"><i class="bi bi-clock me-1"></i> Tiba: {{ $assignment->arrived_at ? $assignment->arrived_at->format('H:i') : '-' }}</small>
        </div>
        <p class="small text-muted mb-3">Setelah pengisian tangki air selesai dan diverifikasi penerima, unggah foto bukti untuk menyelesaikan tugas.</p>
        <button type="button" class="btn btn-success w-100 fw-bold py-3 fs-6 shadow" data-bs-toggle="modal" data-bs-target="#completeModal">
            <i class="bi bi-camera-fill me-2"></i> Unggah Bukti & Selesaikan Tugas
        </button>
    </div>
@elseif($assignment->status === 'Completed')
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-3">
        <i class="bi bi-check-circle-fill fs-3 text-success me-3"></i>
        <div>
            <strong>Pengiriman Telah Selesai!</strong>
            <p class="mb-0 small text-dark">Tugas ini telah berhasil dituntaskan pada {{ $assignment->completed_at ? $assignment->completed_at->format('d/m/Y H:i') : '-' }}.</p>
        </div>
    </div>
@endif

<!-- Job Destination & Customer Details -->
<div class="card driver-card p-3 bg-white mb-3">
    <h6 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">Tujuan Pengiriman & Mitra</h6>
    <div class="mb-2">
        <span class="text-muted small d-block">Perusahaan Pelanggan:</span>
        <h6 class="fw-bold text-dark mb-0">{{ $assignment->order->customer->company_name }}</h6>
    </div>

    <div class="mb-2">
        <span class="text-muted small d-block">Alamat Lengkap Pengiriman:</span>
        <strong class="text-dark small d-block">{{ $assignment->order->delivery_address }}</strong>
    </div>

    <!-- Google Maps Navigation Button -->
    <div class="mt-2 mb-3">
        @php
            $gmapsUrl = "https://www.google.com/maps/dir/?api=1&destination=" . $assignment->order->latitude . "," . $assignment->order->longitude;
        @endphp
        <a href="{{ $gmapsUrl }}" target="_blank" class="btn btn-outline-danger w-100 fw-bold py-2 small shadow-sm">
            <i class="bi bi-google me-1"></i> Buka Rute di Google Maps
        </a>
    </div>

    <div class="row g-2 mb-2">
        <div class="col-6">
            <div class="p-2 rounded bg-light">
                <span class="text-muted small d-block" style="font-size: 0.7rem;">Penerima Lapangan:</span>
                <strong class="small text-dark">{{ $assignment->order->recipient_name }}</strong>
            </div>
        </div>
        <div class="col-6">
            <div class="p-2 rounded bg-light">
                <span class="text-muted small d-block" style="font-size: 0.7rem;">Nomor WhatsApp:</span>
                <strong class="small text-dark">{{ $assignment->order->recipient_phone }}</strong>
            </div>
        </div>
    </div>

    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $assignment->order->recipient_phone) }}?text=Halo%20Pak/Bu%20{{ urlencode($assignment->order->recipient_name) }},%20saya%20driver%20truk%20tangki%20Tirta%20Mandiri%20untuk%20order%20{{ $assignment->order->order_number }}." target="_blank" class="btn btn-success btn-sm w-100 fw-semibold mb-2">
        <i class="bi bi-whatsapp me-1"></i> Hubungi Penerima di Lokasi
    </a>

    @if($assignment->order->notes)
        <div class="p-2 rounded bg-warning-subtle text-dark small mt-2">
            <i class="bi bi-info-circle-fill text-warning me-1"></i> Catatan: <strong>{{ $assignment->order->notes }}</strong>
        </div>
    @endif
</div>

<!-- Fleet & Document Details -->
<div class="card driver-card p-3 bg-white mb-3">
    <h6 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">Informasi Armada Truk & Surat Jalan</h6>
    <div class="row g-2 small">
        <div class="col-6">
            <span class="text-muted d-block" style="font-size: 0.7rem;">Nomor Plat Armada:</span>
            <strong class="text-dark fs-6">{{ $assignment->vehicle->plate_number }}</strong>
        </div>
        <div class="col-6">
            <span class="text-muted d-block" style="font-size: 0.7rem;">Muatan Pesanan:</span>
            <strong class="text-primary-blue fs-6">{{ number_format($assignment->order->volume, 0, ',', '.') }} Liter</strong>
        </div>
        <div class="col-6">
            <span class="text-muted d-block" style="font-size: 0.7rem;">Tipe Armada:</span>
            <span class="text-secondary">{{ $assignment->vehicle->vehicle_type }}</span>
        </div>
        <div class="col-6">
            <span class="text-muted d-block" style="font-size: 0.7rem;">Kapasitas Tangki:</span>
            <span class="text-secondary">{{ number_format($assignment->vehicle->capacity, 0, ',', '.') }} L</span>
        </div>
    </div>

    @if($assignment->document)
        <div class="mt-3 pt-2 border-top">
            <span class="text-muted small d-block" style="font-size: 0.72rem;">No. Surat Izin Jalan Resmi:</span>
            <strong class="text-dark">{{ $assignment->document->document_number }}</strong>
            <a href="{{ route('admin.surat-jalan.show', $assignment->document->id) }}" target="_blank" class="btn btn-link btn-sm p-0 d-block text-start text-primary-blue text-decoration-none mt-1">
                <i class="bi bi-file-earmark-pdf me-1"></i> Lihat Dokumen Digital Surat Jalan
            </a>
        </div>
    @endif
</div>

<!-- Proof of Delivery Card (If completed) -->
@if($assignment->proof)
    <div class="card driver-card p-3 bg-white mb-3">
        <h6 class="fw-bold text-dark-blue mb-2 pb-2 border-bottom">Bukti Pengiriman Terunggah</h6>
        <div class="text-center mb-2">
            <img src="{{ asset('storage/' . $assignment->proof->photo_path) }}" alt="Foto Bukti" class="img-fluid rounded border shadow-sm" style="max-height: 200px;">
        </div>
        <div class="small">
            <div class="mb-1">Penerima: <strong>{{ $assignment->proof->recipient_name }}</strong></div>
            <div class="text-muted">Catatan: {{ $assignment->proof->notes }}</div>
            <div class="text-muted" style="font-size: 0.7rem;">Waktu Selesai: {{ $assignment->proof->completed_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>
@endif

<!-- Button Report Issue (If active) -->
@if(in_array($assignment->status, ['Accepted', 'In Transit', 'Filling Water']))
    <div class="text-center mb-4">
        <button type="button" class="btn btn-outline-danger btn-sm px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#issueModal">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> Laporkan Kendala / Batalkan Pengiriman
        </button>
    </div>
@endif

<!-- Modal: Tolak Job -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('driver.jobs.reject', $assignment->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold text-danger">Tolak Tugas Pengiriman</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">Harap berikan alasan penolakan agar Administrator dapat segera menugaskan driver lain.</p>
                    <label class="form-label small fw-bold text-dark">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control" rows="3" required placeholder="Contoh: Sedang dalam jadwal servis armada atau kondisi kurang sehat..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold">Konfirmasi Tolak Tugas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Laporkan Kendala -->
<div class="modal fade" id="issueModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('driver.jobs.reportIssue', $assignment->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Laporkan Kendala di Lapangan
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">Pesanan akan dialihkan ke status <strong>Pending Review</strong> untuk penanganan Admin.</p>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Jenis Kendala <span class="text-danger">*</span></label>
                        <select name="issue_type" class="form-select" required>
                            <option value="Ban Bocor">Ban Bocor / Masalah Roda</option>
                            <option value="Kendaraan Rusak">Kendaraan Mogok / Mesin Rusak</option>
                            <option value="Jalan Tidak Dapat Dilalui">Jalan Ditutup / Tidak Muat Truk Tangki</option>
                            <option value="Lokasi Tidak Ditemukan">Alamat / Lokasi Tidak Ditemukan</option>
                            <option value="Kendala Teknis">Kendala Teknis Pompa / Selang</option>
                            <option value="Lainnya">Alasan Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Deskripsi & Kronologi Kendala <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Jelaskan kendala secara rinci dan posisi Anda saat ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold">Kirim Laporan Kendala</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Konfirmasi Selesai & Upload Bukti -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('driver.jobs.complete', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold text-success">
                        <i class="bi bi-camera-fill me-1"></i> Unggah Bukti Pengiriman Selesai
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Foto Bukti Pengiriman (Ground Tank / Selang) <span class="text-danger">*</span></label>
                        <input type="file" name="photo" class="form-control" accept="image/*" capture="environment" required>
                        <small class="text-muted" style="font-size: 0.72rem;">Wajib mengunggah foto fisik pengisian air di lokasi.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Nama Penerima yang Memvalidasi</label>
                        <input type="text" name="recipient_name" class="form-control" value="{{ $assignment->order->recipient_name }}" placeholder="Nama penerima di lokasi">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Air 10.000 L telah masuk sempurna ke ground tank."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold">Konfirmasi Selesai & Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

