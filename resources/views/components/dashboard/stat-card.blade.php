@props(['title', 'value', 'icon' => null, 'bg' => 'primary'])

<div class="card">
    <div class="card-body d-flex align-items-center">
        <div class="avatar bg-{{ $bg }} text-white me-3">
            <i class="{{ $icon }}"></i>
        </div>
        <div>
            <h6 class="mb-0">{{ $title }}</h6>
            <h4 class="mb-0 fw-bold">{{ $value }}</h4>
        </div>
    </div>
</div>
