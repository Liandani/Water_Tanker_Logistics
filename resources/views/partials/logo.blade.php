<div class="d-inline-flex align-items-center gap-2">
    <div class="water-logo-badge rounded-circle d-flex align-items-center justify-content-center" style="width: {{ $size ?? 40 }}px; height: {{ $size ?? 40 }}px; background: linear-gradient(135deg, #1687D9, #55C9D9); box-shadow: 0 4px 12px rgba(22, 135, 217, 0.35);">
        <svg xmlns="http://www.w3.org/2000/svg" width="{{ ($size ?? 40) * 0.55 }}" height="{{ ($size ?? 40) * 0.55 }}" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
            <path d="M12 9a3 3 0 0 0-3 3" stroke="#EAF7FF" stroke-width="1.8"></path>
        </svg>
    </div>
    @if(!isset($hideText) || !$hideText)
        <div class="d-flex flex-column text-start">
            <span class="fw-bold tracking-tight text-brand-dark" style="font-size: {{ ($size ?? 40) * 0.42 }}px; line-height: 1.1; color: {{ $textColor ?? '#0B3D62' }};">
                TIRTA<span style="color: #1687D9;">MANDIRI</span>
            </span>
            <small style="font-size: {{ ($size ?? 40) * 0.24 }}px; letter-spacing: 1px; color: #55C9D9; font-weight: 600; text-transform: uppercase;">Water Tanker Logistics</small>
        </div>
    @endif
</div>

