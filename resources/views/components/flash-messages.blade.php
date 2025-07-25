@php
    $types = [
        'success' => ['class' => 'bg-success text-white', 'icon' => 'bx-check-circle'],
        'error' => ['class' => 'bg-danger text-white', 'icon' => 'bx-error-circle'],
        'info' => ['class' => 'bg-info text-white', 'icon' => 'bx-info-circle'],
        'warning' => ['class' => 'bg-warning text-dark', 'icon' => 'bx-error-alt'],
    ];

    $hasErrors = $errors->any();
@endphp

<div aria-live="polite" aria-atomic="true" class="position-relative">
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
        {{-- Flash Messages --}}
        @foreach ($types as $type => $props)
            @if (session($type))
                <div class="toast align-items-center {{ $props['class'] }} border-0 show" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bx {{ $props['icon'] }} me-2"></i>
                            {{ session($type) }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Validation Errors --}}
        @if ($hasErrors)
            <div class="toast align-items-center bg-danger text-white border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bx bx-error me-2"></i>
                        Please fix the following errors:
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>
</div>
