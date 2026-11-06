@extends('layouts.admin')

@section('title', 'Data Armada Truk Tangki')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark-blue mb-1">Manajemen Armada Truk Tangki</h3>
        <p class="text-muted small mb-0">Kelola kendaraan operasional distribusi air, kapasitas tangki, dan status pemeliharaan</p>
    </div>
    <div>
        <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary-blue shadow-sm">
            <i class="bi bi-truck me-1"></i> Tambah Armada Baru
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card card-fresh p-3 mb-4">
    <form method="GET" action="{{ route('admin.vehicles.index') }}" class="row g-2">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari Plat Nomor, Model Truk..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua Status Armada</option>
                <option value="Ready" {{ request('status') === 'Ready' ? 'selected' : '' }}>Ready (Siap Bertugas)</option>
                <option value="On Duty" {{ request('status') === 'On Duty' ? 'selected' : '' }}>On Duty (Sedang Jalan)</option>
                <option value="Maintenance" {{ request('status') === 'Maintenance' ? 'selected' : '' }}>Maintenance (Servis)</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="capacity" class="form-select">
                <option value="">Semua Kapasitas</option>
                <option value="7000" {{ request('capacity') == '7000' ? 'selected' : '' }}>7.000 Liter</option>
                <option value="8000" {{ request('capacity') == '8000' ? 'selected' : '' }}>8.000 Liter</option>
                <option value="16000" {{ request('capacity') == '16000' ? 'selected' : '' }}>16.000 Liter</option>
                <option value="20000" {{ request('capacity') == '20000' ? 'selected' : '' }}>20.000 Liter</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary-blue flex-grow-1">Filter</button>
            @if(request()->hasAny(['search', 'status', 'capacity']))
                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                    <th>Nomor Plat</th>
                    <th>Model / Merek</th>
                    <th>Kategori Kendaraan</th>
                    <th>Kapasitas Tangki</th>
                    <th>Status Armada</th>
                    <th>Tugas Berjalan / Driver</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $v)
                    <tr>
                        <td>
                            <strong class="fs-6 text-dark-blue">{{ $v->plate_number }}</strong>
                        </td>
                        <td>
                            <span class="text-dark">{{ $v->brand_model ?: '-' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $v->vehicle_type }}</span>
                        </td>
                        <td>
                            <strong class="text-primary-blue">{{ number_format($v->capacity, 0, ',', '.') }}</strong> Liter
                        </td>
                        <td>
                            @if($v->status === 'Ready')
                                <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Ready</span>
                            @elseif($v->status === 'On Duty')
                                <span class="badge bg-primary px-3 py-2 rounded-pill"><i class="bi bi-truck me-1"></i> On Duty</span>
                            @else
                                <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="bi bi-tools me-1"></i> Maintenance</span>
                            @endif
                        </td>
                        <td>
                            @if($v->currentAssignment && $v->currentAssignment->driver)
                                <div class="small fw-semibold text-dark">
                                    <i class="bi bi-person-fill text-primary"></i> {{ $v->currentAssignment->driver->user->name }}
                                </div>
                                <small class="text-muted">Order: #{{ $v->currentAssignment->order->order_number }}</small>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.vehicles.edit', $v->id) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($v->status !== 'On Duty')
                                    <button type="button" class="btn btn-outline-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteVehicleModal{{ $v->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteVehicleModal{{ $v->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered text-start">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.vehicles.destroy', $v->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold text-danger">Hapus Armada Truk</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus armada truk tangki <strong>{{ $v->plate_number }}</strong>?
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
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada data armada yang sesuai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($vehicles->hasPages())
        <div class="card-footer bg-white border-top p-3">
            {{ $vehicles->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

