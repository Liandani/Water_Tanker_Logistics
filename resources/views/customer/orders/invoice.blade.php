@extends('layouts.customer')

@section('title', 'Invoice ' . $order->order_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
            <a href="{{ route('customer.orders.show', $order->id) }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left"></i> Kembali ke Detail Pesanan
            </a>
            <button onclick="window.print()" class="btn btn-outline-primary-blue shadow-sm">
                <i class="bi bi-printer-fill me-1"></i> Cetak Dokumen Tagihan (Print)
            </button>
        </div>

        <!-- Official Invoice Card -->
        <div class="card card-fresh p-5 shadow-sm bg-white" id="invoiceCard">
            <!-- Header -->
            <div class="row align-items-center pb-4 mb-4 border-bottom">
                <div class="col-sm-7">
                    @include('partials.logo', ['size' => 44])
                    <p class="text-muted small mt-2 mb-0">
                        {{ $tariff->company_address }}<br>
                        Telp/WA: {{ $tariff->company_phone }} &bull; Email: {{ $tariff->company_email }}
                    </p>
                </div>
                <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
                    <h2 class="fw-bold text-dark-blue mb-1">INVOICE</h2>
                    <span class="fw-semibold text-muted">No: INV-{{ str_replace('ORD-', '', $order->order_number) }}</span>
                    <div class="mt-2">
                        @if($order->payment_status === 'Paid')
                            <span class="badge bg-success fs-6 px-3 py-1">LUNAS / PAID</span>
                        @elseif($order->payment_status === 'Down Payment')
                            <span class="badge bg-warning text-dark fs-6 px-3 py-1">DOWN PAYMENT</span>
                        @else
                            <span class="badge bg-danger fs-6 px-3 py-1">BELUM LUNAS / UNPAID</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bill To & Transaction Info -->
            <div class="row g-3 mb-4 pb-3">
                <div class="col-sm-6">
                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px;">TAGIHAN KEPADA:</span>
                    <h5 class="fw-bold text-dark mt-1 mb-1">{{ $order->customer->company_name }}</h5>
                    <p class="text-muted small mb-0">
                        u.p. {{ $order->recipient_name ?? $order->customer->contact_person }}<br>
                        {{ $order->delivery_address }}<br>
                        Telp: {{ $order->recipient_phone ?? $order->customer->user->phone }}
                    </p>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <div class="small mb-1">
                        <span class="text-muted">Tanggal Transaksi:</span>
                        <strong class="text-dark ms-2">{{ $order->created_at->format('d F Y') }}</strong>
                    </div>
                    <div class="small mb-1">
                        <span class="text-muted">Tanggal Pengiriman:</span>
                        <strong class="text-dark ms-2">{{ $order->delivery_date->format('d F Y') }}</strong>
                    </div>
                    <div class="small mb-1">
                        <span class="text-muted">No. Pesanan:</span>
                        <strong class="text-primary-blue ms-2">{{ $order->order_number }}</strong>
                    </div>
                    @if($order->document)
                        <div class="small">
                            <span class="text-muted">Surat Izin Jalan:</span>
                            <strong class="text-dark ms-2">{{ $order->document->document_number }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Table Items -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered border-light-subtle align-middle">
                    <thead style="background-color: #F5FAFD;">
                        <tr class="text-dark-blue small">
                            <th class="py-3">Deskripsi Layanan</th>
                            <th class="text-center py-3">Kuantitas</th>
                            <th class="text-end py-3">Tarif Satuan (Rp)</th>
                            <th class="text-end py-3">Subtotal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>Pasokan Air Bersih Truk Tangki</strong>
                                <small class="text-muted d-block">Air baku bersih, higienis, dan food-grade sesuai standar baku mutu</small>
                            </td>
                            <td class="text-center">{{ number_format($order->volume, 0, ',', '.') }} Liter</td>
                            <td class="text-end">{{ number_format($order->water_price_rate, 0, ',', '.') }} /L</td>
                            <td class="text-end fw-semibold">{{ number_format($order->water_price, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Biaya Pengiriman & Transportasi Armada</strong>
                                <small class="text-muted d-block">
                                    Alokasi {{ $order->fleet_category_recommendation }} (Jarak: {{ $order->estimated_distance }} km, {{ $order->trips_count }} Trip)
                                </small>
                            </td>
                            <td class="text-center">{{ $order->trips_count }} Trip</td>
                            <td class="text-end">-</td>
                            <td class="text-end fw-semibold">{{ number_format($order->delivery_price, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold py-3 text-dark-blue fs-5">TOTAL PEMBAYARAN:</td>
                            <td class="text-end fw-bold py-3 text-primary-blue fs-5">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Payment instructions & signatures -->
            <div class="row g-4 mt-2 pt-3 border-top">
                <div class="col-sm-7 small">
                    <h6 class="fw-bold text-dark-blue mb-2">Instruksi Pembayaran Bank Transfer:</h6>
                    <div class="p-3 rounded-3 bg-light border">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Bank:</span>
                            <strong>Bank Central Asia (BCA)</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">No. Rekening:</span>
                            <strong class="text-primary-blue fs-6">8820-9988-7711</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Atas Nama:</span>
                            <strong>PT Tirta Mandiri Logistik</strong>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5 text-center mt-3 mt-sm-0">
                    <span class="small text-muted d-block mb-4">Hormat Kami,<br><strong>Tirta Mandiri Water Logistics</strong></span>
                    <div style="height: 50px;"></div>
                    <span class="fw-bold text-dark border-top pt-1 d-inline-block px-4">Finance & Administration</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

