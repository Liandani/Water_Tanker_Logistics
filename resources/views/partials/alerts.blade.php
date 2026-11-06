@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm border-0 mb-4" role="alert" style="background-color: #EAF9F4; border-left: 4px solid #20B486 !important; color: #0f5132;">
        <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
        <div class="flex-grow-1">
            <strong>Berhasil!</strong> {{ session('success') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm border-0 mb-4" role="alert" style="background-color: #FDF2F4; border-left: 4px solid #E85D75 !important; color: #842029;">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-danger"></i>
        <div class="flex-grow-1">
            <strong>Perhatian:</strong> {{ session('error') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center shadow-sm border-0 mb-4" role="alert" style="background-color: #FEF9EE; border-left: 4px solid #F5B942 !important; color: #664d03;">
        <i class="bi bi-exclamation-circle-fill me-2 fs-5" style="color: #F5B942;"></i>
        <div class="flex-grow-1">
            <strong>Informasi:</strong> {{ session('warning') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center shadow-sm border-0 mb-4" role="alert" style="background-color: #EAF7FF; border-left: 4px solid #1687D9 !important; color: #0B3D62;">
        <i class="bi bi-info-circle-fill me-2 fs-5 text-primary"></i>
        <div class="flex-grow-1">
            {{ session('info') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="background-color: #FDF2F4; border-left: 4px solid #E85D75 !important; color: #842029;">
        <div class="d-flex align-items-center mb-1">
            <i class="bi bi-x-circle-fill me-2 fs-5 text-danger"></i>
            <strong>Terdapat beberapa kesalahan input:</strong>
        </div>
        <ul class="mb-0 ps-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

