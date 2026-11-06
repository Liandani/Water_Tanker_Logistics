@extends('layouts.admin')

@section('title', 'Surat Izin Jalan ' . $document->document_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
            <a href="{{ route('admin.surat-jalan.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Dokumen
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.surat-jalan.print', $document->id) }}" target="_blank" class="btn btn-outline-secondary">
                    <i class="bi bi-printer-fill me-1"></i> Print Dokumen
                </a>
                <a href="{{ route('admin.surat-jalan.pdf', $document->id) }}" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Unduh PDF Resmi
                </a>
            </div>
        </div>

        <!-- Document Preview Sheet -->
        <div class="card card-fresh p-5 bg-white shadow-sm border" style="font-family: 'Inter', sans-serif;">
            <!-- Header with Company Logo -->
            <div class="row align-items-center pb-3 mb-4 border-bottom border-dark border-2">
                <div class="col-sm-7">
                    @include('partials.logo', ['size' => 48])
                    <div class="small text-muted mt-2">
                        <strong>{{ $tariff->company_name }}</strong><br>
                        {{ $tariff->company_address }}<br>
                        Telp: {{ $tariff->company_phone }} &bull; Email: {{ $tariff->company_email }}
                    </div>
                </div>
                <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
                    <h3 class="fw-bold text-dark-blue mb-1" style="letter-spacing: 1px;">SURAT IZIN JALAN</h3>
                    <div class="small fw-bold text-danger">NOMOR: {{ $document->document_number }}</div>
                    <small class="text-muted d-block">Tanggal: {{ $document->issued_at->format('d F Y - H:i') }} WIB</small>
                    @if($document->revision > 1)
                        <span class="badge bg-warning text-dark mt-1">Revisi Ke-{{ $document->revision }}</span>
                    @endif
                </div>
            </div>

            <!-- Notice -->
            <div class="p-2 mb-4 bg-light border text-center small text-secondary">
                Dokumen ini merupakan surat izin jalan resmi angkutan armada truk tangki air bersih untuk keperluan pengiriman kepada mitra yang dituju.
            </div>

            <!-- Content Grid -->
            <div class="row g-4 mb-4">
                <div class="col-sm-6">
                    <h6 class="fw-bold text-dark-blue border-bottom pb-1 mb-2">I. DATA TUJUAN PENGIRIMAN</h6>
                    <table class="table table-sm table-borderless small mb-0">
                        <tr>
                            <td class="text-muted" style="width: 140px;">Nama Pelanggan:</td>
                            <td class="fw-bold text-dark">{{ $document->order->customer->company_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Penerima di Lokasi:</td>
                            <td class="fw-bold text-dark">{{ $document->order->recipient_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kontak Penerima:</td>
                            <td>{{ $document->order->recipient_phone }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat Bongkar:</td>
                            <td>{{ $document->order->delivery_address }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. Pesanan:</td>
                            <td class="fw-bold text-primary-blue">{{ $document->order->order_number }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-sm-6">
                    <h6 class="fw-bold text-dark-blue border-bottom pb-1 mb-2">II. DATA PENGEMUDI & ARMADA</h6>
                    <table class="table table-sm table-borderless small mb-0">
                        <tr>
                            <td class="text-muted" style="width: 140px;">Nama Pengemudi:</td>
                            <td class="fw-bold text-dark">{{ $document->assignment->driver->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nomor HP Driver:</td>
                            <td>{{ $document->assignment->driver->user->phone }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nomor SIM Driver:</td>
                            <td>{{ $document->assignment->driver->license_number ?? 'B2 UMUM' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nomor Plat Polisi:</td>
                            <td class="fw-bold fs-6 text-dark">{{ $document->assignment->vehicle->plate_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jenis Armada:</td>
                            <td>{{ $document->assignment->vehicle->vehicle_type }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Muatan Table -->
            <h6 class="fw-bold text-dark-blue border-bottom pb-1 mb-2">III. RINCIAN MUATAN AIR TANGKI</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered small align-middle">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-center" style="width: 50px;">No.</th>
                            <th>Uraian Muatan Barang</th>
                            <th class="text-center">Kapasitas Tangki</th>
                            <th class="text-center">Volume Dikirim</th>
                            <th>Keterangan / Segel</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">1</td>
                            <td>
                                <strong>Air Bersih Baku Higienis (Food Grade)</strong><br>
                                <small class="text-muted">Distribusi melalui truk tangki berstandar pipa hisap & segel aman</small>
                            </td>
                            <td class="text-center">{{ number_format($document->assignment->vehicle->capacity, 0, ',', '.') }} Liter</td>
                            <td class="text-center fw-bold fs-6 text-primary-blue">{{ number_format($document->order->volume, 0, ',', '.') }} Liter</td>
                            <td>Segel Tangki Utuh Terpasang</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-4 small">
                <strong>Catatan Khusus:</strong>
                <p class="text-muted mb-0">{{ $document->order->notes ?: 'Truk diizinkan masuk lokasi pembongkaran. Pengisian wajib didampingi petugas penerima.' }}</p>
            </div>

            <!-- Signature Zones: Admin, Driver, Recipient -->
            <div class="row text-center mt-5 pt-3 g-4">
                <div class="col-4">
                    <span class="small text-muted d-block">Petugas Pengawas / Admin:</span>
                    <div style="height: 70px;"></div>
                    <span class="fw-bold text-dark border-top pt-1 d-inline-block px-3 small">
                        ({{ $document->issuer->name ?? 'Administrator' }})
                    </span>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">Operasional & Logistik</small>
                </div>
                <div class="col-4">
                    <span class="small text-muted d-block">Pengemudi / Driver:</span>
                    <div style="height: 70px;"></div>
                    <span class="fw-bold text-dark border-top pt-1 d-inline-block px-3 small">
                        ({{ $document->assignment->driver->user->name }})
                    </span>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">Pengemudi Armada</small>
                </div>
                <div class="col-4">
                    <span class="small text-muted d-block">Penerima Lapangan:</span>
                    <div style="height: 70px;"></div>
                    <span class="fw-bold text-dark border-top pt-1 d-inline-block px-3 small">
                        ({{ $document->order->recipient_name ?? 'Penerima Barang' }})
                    </span>
                    <small class="text-muted d-block" style="font-size: 0.7rem;">Cap / Tanda Tangan Penerima</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

