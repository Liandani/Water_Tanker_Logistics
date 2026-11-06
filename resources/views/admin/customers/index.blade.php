@extends('layouts.admin')

@section('title', 'Data Pelanggan')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark-blue mb-1">Manajemen Data Pelanggan</h3>
        <p class="text-muted small mb-0">Kelola informasi mitra industri, hotel, konstruksi, dan restoran</p>
    </div>
    <div>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary-blue shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pelanggan Baru
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card card-fresh p-3 mb-4">
    <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-2">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari Nama Perusahaan, PIC, Email, Telp..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select">
                <option value="">Semua Kategori Bisnis</option>
                <option value="Hotel" {{ request('category') === 'Hotel' ? 'selected' : '' }}>Hotel & Penginapan</option>
                <option value="Proyek Konstruksi" {{ request('category') === 'Proyek Konstruksi' ? 'selected' : '' }}>Proyek Konstruksi</option>
                <option value="Pabrik/Industri" {{ request('category') === 'Pabrik/Industri' ? 'selected' : '' }}>Pabrik / Industri</option>
                <option value="Restoran" {{ request('category') === 'Restoran' ? 'selected' : '' }}>Restoran & Cafe</option>
                <option value="Lainnya" {{ request('category') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary-blue flex-grow-1">Filter</button>
            @if(request()->hasAny(['search', 'category']))
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                    <th>Perusahaan / Mitra</th>
                    <th>Penanggung Jawab (PIC)</th>
                    <th>Kontak</th>
                    <th>Kategori Bisnis</th>
                    <th>Alamat</th>
                    <th>Total Order</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                    <tr>
                        <td>
                            <a href="{{ route('admin.customers.show', $c->id) }}" class="fw-bold text-decoration-none text-primary-blue">
                                {{ $c->company_name }}
                            </a>
                        </td>
                        <td>
                            <strong class="text-dark">{{ $c->contact_person }}</strong>
                        </td>
                        <td>
                            <div class="small text-dark"><i class="bi bi-envelope me-1 text-muted"></i> {{ $c->user->email }}</div>
                            <div class="small text-secondary"><i class="bi bi-telephone me-1 text-muted"></i> {{ $c->user->phone ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary-blue border">{{ $c->business_category }}</span>
                        </td>
                        <td>
                            <small class="text-secondary d-block" style="max-width: 200px;">{{ Str::limit($c->address, 60) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1">{{ $c->orders_count }} Pesanan</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.customers.show', $c->id) }}" class="btn btn-outline-secondary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.customers.edit', $c->id) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Hapus" data-bs-toggle="modal" data-bs-target="#deleteCustomerModal{{ $c->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteCustomerModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered text-start">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.customers.destroy', $c->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold text-danger">Hapus Pelanggan</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus pelanggan <strong>{{ $c->company_name }}</strong> beserta akun login dan riwayat terkait? Tindakan ini tidak dapat dibatalkan.
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
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada data pelanggan yang sesuai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
        <div class="card-footer bg-white border-top p-3">
            {{ $customers->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

