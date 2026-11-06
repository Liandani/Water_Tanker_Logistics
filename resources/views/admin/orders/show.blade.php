@extends('layouts.admin')

@section('title', 'Detail Pesanan ' . $order->order_number)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left"></i> Kembali ke Manajemen Pesanan
        </a>
        <div class="d-flex align-items-center gap-2 mt-1">
            <h3 class="fw-bold text-dark-blue mb-0">Pesanan #{{ $order->order_number }}</h3>
            <span class="badge {{ $order->status === 'Selesai' ? 'badge-selesai' : ($order->status === 'Dikirim' ? 'badge-dikirim' : ($order->status === 'Diproses' ? 'badge-diproses' : ($order->status === 'Pending Review' ? 'badge-kendala' : ($order->status === 'Pending' ? 'badge-pending' : 'badge-dibatalkan')))) }} px-3 py-2 fs-6 rounded-pill">
                {{ $order->status }}
            </span>
        </div>
    </div>
    <div class="d-flex gap-2">
        @if($order->status === 'Pending')
            <button type="button" class="btn btn-success fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#approveModal">
                <i class="bi bi-check-circle-fill me-1"></i> Approve & Tugaskan Armada
            </button>
            <button type="button" class="btn btn-outline-warning fw-bold" data-bs-toggle="modal" data-bs-target="#pendingModal">
                <i class="bi bi-pause-circle me-1"></i> Tunda (Pending)
            </button>
            <button type="button" class="btn btn-outline-danger fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-circle me-1"></i> Tolak (Reject)
            </button>
        @elseif($order->status === 'Pending Review')
            <button type="button" class="btn btn-warning fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#reassignModal">
                <i class="bi bi-arrow-repeat me-1"></i> Tugaskan Ulang Driver & Armada
            </button>
        @endif

        @if($order->documents->count() > 0)
            <a href="{{ route('admin.surat-jalan.show', $order->documents->last()->id) }}" class="btn btn-outline-primary-blue shadow-sm">
                <i class="bi bi-file-earmark-text me-1"></i> Surat Izin Jalan (Digital)
            </a>
        @endif
    </div>
</div>

<!-- Reason Notices if Any -->
@if($order->pending_reason)
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" style="background-color: #FEF9EE; border-left: 4px solid #F5B942 !important;">
        <i class="bi bi-exclamation-circle-fill fs-4 me-3 text-warning"></i>
        <div>
            <strong>Catatan Penundaan / Kendala Driver:</strong>
            <p class="mb-0 small text-dark mt-1">{{ $order->pending_reason }}</p>
        </div>
    </div>
@endif

@if($order->rejection_reason)
    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4" style="background-color: #FDF2F4; border-left: 4px solid #E85D75 !important;">
        <i class="bi bi-x-circle-fill fs-4 me-3 text-danger"></i>
        <div>
            <strong>Alasan Penolakan Pesanan:</strong>
            <p class="mb-0 small text-dark mt-1">{{ $order->rejection_reason }}</p>
        </div>
    </div>
@endif

<div class="row g-4 mb-4">
    <!-- Left Column: Details -->
    <div class="col-lg-8">
        <!-- Delivery Specs -->
        <div class="card card-fresh p-4 mb-4">
            <h5 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">Spesifikasi Permintaan Air Tangki</h5>
            <div class="row g-3">
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Volume yang Dipesan:</span>
                    <h4 class="fw-bold text-primary-blue mb-0">{{ number_format($order->volume, 0, ',', '.') }} Liter</h4>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Rekomendasi Kategori Armada:</span>
                    <span class="badge bg-light text-dark border fs-6 mt-1">{{ $order->fleet_category_recommendation }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Tanggal Pengiriman yang Diminta:</span>
                    <strong class="text-dark">{{ $order->delivery_date->format('d F Y') }}</strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Estimasi Jarak dari Depo:</span>
                    <strong class="text-dark">{{ $order->estimated_distance }} km</strong>
                </div>
                <div class="col-12">
                    <span class="text-muted small d-block">Alamat Lengkap Pengiriman:</span>
                    <strong class="text-dark d-block">{{ $order->delivery_address }}</strong>
                    <small class="text-muted">Koordinat: {{ $order->latitude }}, {{ $order->longitude }}</small>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Kontak Penerima di Lokasi:</span>
                    <strong class="text-dark">{{ $order->recipient_name }}</strong> ({{ $order->recipient_phone }})
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Catatan Pelanggan:</span>
                    <span class="text-secondary">{{ $order->notes ?: 'Tidak ada catatan khusus.' }}</span>
                </div>
            </div>
        </div>

        <!-- Assignment History & Current Status -->
        <div class="card card-fresh p-4 mb-4">
            <h5 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">Riwayat Penugasan Driver & Armada</h5>
            @forelse($order->assignments as $assign)
                <div class="p-3 rounded-3 bg-light mb-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-dark">Driver: {{ $assign->driver->user->name }} &bull; Armada: {{ $assign->vehicle->plate_number }}</strong>
                        <span class="badge {{ $assign->status === 'Completed' ? 'bg-success' : ($assign->status === 'Rejected' || $assign->status === 'Cancelled' ? 'bg-danger' : 'bg-primary') }}">
                            {{ $assign->status }}
                        </span>
                    </div>
                    <div class="row g-2 small text-secondary">
                        <div class="col-sm-4">Ditugaskan: {{ $assign->assigned_at->format('d/m/Y H:i') }}</div>
                        <div class="col-sm-4">Diterima: {{ $assign->accepted_at ? $assign->accepted_at->format('H:i') : '-' }}</div>
                        <div class="col-sm-4">Berangkat: {{ $assign->started_at ? $assign->started_at->format('H:i') : '-' }}</div>
                        <div class="col-sm-4">Tiba: {{ $assign->arrived_at ? $assign->arrived_at->format('H:i') : '-' }}</div>
                        <div class="col-sm-4">Selesai: {{ $assign->completed_at ? $assign->completed_at->format('H:i') : '-' }}</div>
                    </div>
                    @if($assign->driver_reject_reason)
                        <div class="small text-danger mt-2">
                            <strong>Alasan Penolakan:</strong> {{ $assign->driver_reject_reason }}
                        </div>
                    @endif
                    @if($assign->driver_issue_reason)
                        <div class="small text-danger mt-2">
                            <strong>Laporan Kendala Driver:</strong> {{ $assign->driver_issue_reason }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-4 text-muted small">
                    Belum ada penugasan armada yang tercatat.
                </div>
            @endforelse
        </div>

        <!-- Proof of Delivery (if completed) -->
        @if($order->activeAssignment && $order->activeAssignment->proof)
            @php $proof = $order->activeAssignment->proof; @endphp
            <div class="card card-fresh p-4 mb-4 border-success">
                <h5 class="fw-bold text-success mb-3 pb-2 border-bottom">
                    <i class="bi bi-camera-fill me-2"></i> Bukti Pengiriman Selesai (Foto & Tanda Tangan)
                </h5>
                <div class="row g-3 align-items-center">
                    <div class="col-md-5 text-center">
                        <img src="{{ asset('storage/' . $proof->photo_path) }}" alt="Foto Bukti" class="img-fluid rounded border shadow-sm" style="max-height: 220px;">
                    </div>
                    <div class="col-md-7">
                        <div class="mb-2">
                            <span class="text-muted small d-block">Penerima yang Memvalidasi:</span>
                            <strong class="text-dark">{{ $proof->recipient_name }}</strong>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted small d-block">Catatan Penyelesaian:</span>
                            <span class="text-secondary">{{ $proof->notes }}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted small d-block">Waktu Selesai Pengisian:</span>
                            <strong class="text-dark">{{ $proof->completed_at->format('d F Y - H:i') }} WIB</strong>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column: Customer Info & Financial Breakdown -->
    <div class="col-lg-4">
        <!-- Customer Info -->
        <div class="card card-fresh p-4 mb-4">
            <h6 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">Data Pelanggan</h6>
            <div class="mb-2">
                <span class="text-muted small d-block">Perusahaan:</span>
                <a href="{{ route('admin.customers.show', $order->customer_id) }}" class="fw-bold text-dark text-decoration-none">
                    {{ $order->customer->company_name }}
                </a>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block">Penanggung Jawab:</span>
                <strong class="text-dark">{{ $order->customer->contact_person }}</strong>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block">Kontak WhatsApp:</span>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer->user->phone) }}" target="_blank" class="small text-success fw-bold text-decoration-none">
                    <i class="bi bi-whatsapp me-1"></i> {{ $order->customer->user->phone ?? '-' }}
                </a>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block">Kategori Bisnis:</span>
                <span class="badge bg-light text-primary-blue border">{{ $order->customer->business_category }}</span>
            </div>
        </div>

        <!-- Billing Summary -->
        <div class="card card-fresh p-4 mb-4">
            <h6 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">Rincian Finansial Pesanan</h6>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Tarif Air (Rp {{ number_format($order->water_price_rate, 0, ',', '.') }}/L):</span>
                <span class="text-dark">Rp {{ number_format($order->water_price, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Ongkos Kirim ({{ $order->estimated_distance }} km):</span>
                <span class="text-dark">Rp {{ number_format($order->delivery_price, 0, ',', '.') }}</span>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-dark-blue">Total Tagihan:</span>
                <h4 class="fw-bold text-primary-blue mb-0">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h4>
            </div>

            <div class="d-flex justify-content-between mb-3 small">
                <span class="text-muted">Status Pembayaran:</span>
                <span class="badge {{ $order->payment_status === 'Paid' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-1">
                    {{ $order->payment_status }}
                </span>
            </div>

            @if($order->financialTransaction)
                <div class="p-2 rounded bg-success-subtle text-success small border border-success-subtle">
                    <i class="bi bi-check-circle-fill me-1"></i> Pemasukan telah tercatat otomatis di modul Keuangan.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Approve & Assign -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-success">
                        <i class="bi bi-check-circle-fill me-1"></i> Persetujuan Order & Penugasan Armada
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info small mb-3">
                        Volume pesanan: <strong>{{ number_format($order->volume, 0, ',', '.') }} Liter</strong>.
                        Rekomendasi Armada: <strong>{{ $order->fleet_category_recommendation }}</strong>.
                        Sistem akan memvalidasi kecukupan kapasitas armada dan otomatis menerbitkan <strong>Surat Izin Jalan Resmi</strong>.
                    </div>

                    <div class="row g-3">
                        <!-- Driver Selection -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Pilih Driver Aktif <span class="text-danger">*</span></label>
                            <select name="driver_id" class="form-select" required>
                                <option value="" selected disabled>Pilih Driver Tersedia...</option>
                                @foreach($availableDrivers as $d)
                                    <option value="{{ $d->id }}">{{ $d->user->name }} (SIM: {{ $d->license_number ?? 'B2' }}) - Rating {{ $d->averageRating() }}⭐</option>
                                @endforeach
                            </select>
                            @if($availableDrivers->isEmpty())
                                <small class="text-danger d-block mt-1">Tidak ada driver aktif yang sedang standby. Periksa menu Data Driver.</small>
                            @endif
                        </div>

                        <!-- Vehicle Selection -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Pilih Armada Truk Tangki (Ready) <span class="text-danger">*</span></label>
                            <select name="vehicle_id" class="form-select" required>
                                <option value="" selected disabled>Pilih Truk Tangki Ready...</option>
                                @foreach($availableVehicles as $v)
                                    <option value="{{ $v->id }}">
                                        {{ $v->plate_number }} - {{ $v->vehicle_type }} (Kapasitas: {{ number_format($v->capacity, 0, ',', '.') }} L)
                                    </option>
                                @endforeach
                            </select>
                            @if($availableVehicles->isEmpty())
                                <small class="text-danger d-block mt-1">Tidak ada armada berstatus Ready. Periksa menu Data Armada.</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="bi bi-check-lg me-1"></i> Setujui & Terbitkan Surat Jalan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Reassign -->
<div class="modal fade" id="reassignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.orders.reassign', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-warning">
                        <i class="bi bi-arrow-repeat me-1"></i> Penugasan Ulang Armada Pengganti
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning small mb-3">
                        Pilih driver dan armada baru pengganti. Dokumen Surat Izin Jalan akan direvisi otomatis.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Pilih Driver Pengganti <span class="text-danger">*</span></label>
                            <select name="driver_id" class="form-select" required>
                                <option value="" selected disabled>Pilih Driver Tersedia...</option>
                                @foreach($availableDrivers as $d)
                                    <option value="{{ $d->id }}">{{ $d->user->name }} (SIM: {{ $d->license_number ?? 'B2' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Pilih Armada Pengganti (Ready) <span class="text-danger">*</span></label>
                            <select name="vehicle_id" class="form-select" required>
                                <option value="" selected disabled>Pilih Truk Tangki Ready...</option>
                                @foreach($availableVehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->plate_number }} - {{ $v->vehicle_type }} ({{ number_format($v->capacity, 0, ',', '.') }} L)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4">
                        <i class="bi bi-check-lg me-1"></i> Tugaskan Ulang Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Pending -->
<div class="modal fade" id="pendingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.orders.pending', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold text-warning">Tunda Pesanan (Pending)</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">Admin wajib memasukkan alasan penundaan pengiriman agar diinformasikan kepada pelanggan.</p>
                    <label class="form-label small fw-bold text-dark">Alasan Penundaan <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control" rows="3" required placeholder="Contoh: Menunggu ketersediaan pasokan debit air bersih atau armada tangki 20.000 L sedang antre pengisian..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-warning fw-bold">Konfirmasi Tunda Pesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.orders.reject', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold text-danger">Tolak Pesanan (Reject)</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">Admin wajib memasukkan alasan penolakan pesanan ini.</p>
                    <label class="form-label small fw-bold text-dark">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control" rows="3" required placeholder="Contoh: Lokasi berada di luar jangkauan radius operasional armada atau akses jalan tidak dapat dilalui truk tangki..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold">Tolak Pesanan Ini</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

