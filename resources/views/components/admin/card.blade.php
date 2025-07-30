@props([
    'icon' => 'bar-chart-2',
    'label' => '',
    'value' => '',
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'card shadow-sm mb-4']) }}>
    <div class="card-body d-flex align-items-center">
        <div class="avatar bg-primary text-white rounded-circle shadow-sm me-3"
            style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-{{ $icon }}"></i>
        </div>
        <div>
            <h6 class="mb-1 text-muted">{{ $label }}</h6>
            <h4 class="mb-0 {{ $class }}">{{ $value }}</h4>
        </div>
    </div>
</div>
