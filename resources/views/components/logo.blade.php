@props([
    'width' => '170px',
    'showTitle' => true,
])

<div class="d-flex align-items-center gap-2">
    <div class="text-center">
        @if($showTitle)
        <img src="{{ asset('assets/images/logo-provinsi.png') }}" alt="Logo Provinsi Bali" class="me-3" style="width: 60px; height: auto;">
        <div>
            <h4 class="mb-0 fw-bold text-primary">BAPENDA</h4>
            <p class="mb-0 text-muted small">Provinsi Bali</p>
        </div>
    @endif
    </div>
    <img src="{{ asset('assets/images/logo.svg') }}" alt="logo" width="{{ $width }}">
</div>

