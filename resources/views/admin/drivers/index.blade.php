@extends('layouts.admin')

@section('title', 'Data Driver Truk Tangki')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark-blue mb-1">Manajemen Data Driver</h3>
        <p class="text-muted small mb-0">Kelola pengemudi armada truk tangki, status kerja, verifikasi, dan rating</p>
    </div>
    <div>
        <a href="{{ route('admin.drivers.create') }}" class="btn btn-primary-blue shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Driver Baru
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card card-fresh p-3 mb-4">
    <form method="GET" action="{{ route('admin.drivers.index') }}" class="row g-2">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari Nama Driver, No. SIM, HP..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="availability" class="form-select">
                <option value="">Semua Kesiapan Kerja</option>
                <option value="Aktif" {{ request('availability') === 'Aktif' ? 'selected' : '' }}>Aktif (Ready Bekerja)</option>
                <option value="On Delivery" {{ request('availability') === 'On Delivery' ? 'selected' : '' }}>On Delivery (Di Jalan)</option>
                <option value="Libur" {{ request('availability') === 'Libur' ? 'selected' : '' }}>Libur</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="verification" class="form-select">
                <option value="">Semua Verifikasi</option>
                <option value="verified" {{ request('verification') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="pending" {{ request('verification') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="rejected" {{ request('verification') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary-blue flex-grow-1">Filter</button>
            @if(request()->hasAny(['search', 'availability', 'verification']))
                <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card card-fresh">
    <div class="table-responsive">
        <table class="table table-fresh align-middle mb-0">
            <thead>
                <tr>
                    <th>Driver</th>
                    <th>Kontak WhatsApp</th>
                    <th>Nomor SIM</th>
                    <th>Kesiapan Kerja</th>
                    <th>Verifikasi</th>
                    <th>Rating Pelanggan</th>
                    <th class="text-end">Aksi & Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($drivers as $d)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary-blue text-white d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                    {{ substr($d->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.drivers.show', $d->id) }}" class="fw-bold text-decoration-none text-primary-blue">
                                        {{ $d->user->name }}
                                    </a>
                                    <small class="text-muted d-block">{{ $d->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-dark small"><i class="bi bi-whatsapp text-success me-1"></i> {{ $d->user->phone ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $d->license_number ?? 'Belum Diisi' }}</span>
                        </td>
                        <td>
                            @if($d->availability_status === 'Aktif')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Aktif</span>
                            @elseif($d->availability_status === 'On Delivery')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><i class="bi bi-truck me-1"></i> On Delivery</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1"><i class="bi bi-pause-circle me-1"></i> Libur</span>
                            @endif
                        </td>
                        <td>
                            @if($d->verification_status === 'verified')
                                <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> Verified</span>
                            @elseif($d->verification_status === 'pending')
                                <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-clock me-1"></i> Menunggu</span>
                            @else
                                <span class="badge bg-danger px-2 py-1">Rejected</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 text-warning small">
                                <i class="bi bi-star-fill"></i>
                                <strong class="text-dark">{{ $d->averageRating() }}</strong>
                                <span class="text-muted">({{ $d->ratings->count() }})</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                @if($d->verification_status === 'pending')
                                    <form action="{{ route('admin.drivers.verify', $d->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-sm btn-success fw-bold py-1 px-2" title="Verifikasi Driver">
                                            <i class="bi bi-check-lg"></i> Terima
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.drivers.verify', $d->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-sm btn-outline-danger fw-bold py-1 px-2" title="Tolak Pendaftaran">
                                            <i class="bi bi-x-lg"></i> Tolak
                                        </button>
                                    </form>
                                @endif

                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.drivers.show', $d->id) }}" class="btn btn-outline-secondary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.drivers.edit', $d->id) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteDriverModal{{ $d->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteDriverModal{{ $d->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered text-start">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.drivers.destroy', $d->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold text-danger">Hapus Data Driver</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus data Driver <strong>{{ $d->user->name }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-danger fw-bold">Hapus Sekarang</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada data driver yang sesuai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($drivers->hasPages())
        <div class="card-footer bg-white border-top p-3">
            {{ $drivers->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

