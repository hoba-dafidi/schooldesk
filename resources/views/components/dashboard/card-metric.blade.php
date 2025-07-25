@props([
    'icon',
    'title',
    'amount',
    'trendColor' => 'success',
    'trendIcon' => 'bx-up-arrow-alt',
    'trendPercent' => '0%',
    'compact' => false,
])

<div class="card {{ $compact ? '' : 'h-100' }}">
    <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between mb-4">
            <div class="avatar flex-shrink-0">
                <img src="{{ asset($icon) }}" alt="{{ $title }}" class="rounded">
            </div>
            <div class="dropdown">
                <button class="btn p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded text-muted"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="#">View More</a>
                    <a class="dropdown-item" href="#">Delete</a>
                </div>
            </div>
        </div>
        <p class="mb-1">{{ $title }}</p>
        <h4 class="card-title mb-3">{{ $amount }}</h4>
        <small class="text-{{ $trendColor }} fw-medium">
            <i class='bx {{ $trendIcon }}'></i> {{ $trendPercent }}
        </small>
    </div>
</div>
