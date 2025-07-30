@extends('layouts/contentNavbarLayout')

@section('title', 'Manage Schools')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4 d-flex justify-content-between align-items-center">
            Manage Schools
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#schoolModal" onclick="resetSchoolForm()">
                <i class="bx bx-plus"></i> Create New School
            </button>
        </h4>

        <!-- Alert Notifications -->
        <div id="schoolAlert" class="alert d-none" role="alert"></div>

        <!-- Schools List as Table -->
        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered" id="schoolsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schools as $school)
                            <tr>
                                <td>
                                    @if ($school->logo)
                                        <a href="{{ asset('storage/' . $school->logo) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo"
                                                class="rounded" style="height: 40px;">
                                        </a>
                                    @else
                                        <span class="text-muted">No Logo</span>
                                    @endif
                                </td>
                                <td>{{ $school->name }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $school->status === 'active' ? 'success' : 'warning' }}">
                                        {{ ucfirst($school->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary">
                                        {{ $school->type }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        {{-- Edit Button --}}
                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Edit School"
                                            onclick='editSchool(@json($school))' data-bs-toggle="modal"
                                            data-bs-target="#schoolModal">
                                            <i class="bx bx-edit"></i>
                                        </button>

                                        {{-- Subscribe Button --}}
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Subscribe to Plan"
                                            data-bs-target="#subscriptionModal" data-school-id="{{ $school->id }}">
                                            <i class="bx bx-dollar-circle"></i>
                                        </button>

                                        {{-- View Subscription History --}}
                                        <a href="{{ route('user.schools.subscriptions.history', $school->id) }}"
                                            class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="View Subscription History">
                                            <i class="bx bx-history"></i>
                                        </a>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('user.schools.destroy', $school->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this school?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Delete School">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>

                                    </div>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No schools found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @include('user.schools.modals.subscription-modal')
        @include('user.schools.modals.school-modal')
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner"
        class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-light bg-opacity-50 d-none"
        style="z-index:1055">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
@endsection


@push('scripts')
    @include('user.schools.partials.school-js')
@endpush
@push('styles')
    <style>
        /* Modal fade in animation for modal-content */
        .modal.fade .modal-dialog {
            transform: translateY(-30px);
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        }

        .modal.fade.show .modal-dialog {
            transform: translateY(0);
            opacity: 1;
        }

        /* Smooth fade in for current subscription info */
        #currentSubscriptionInfo.show {
            opacity: 1 !important;
        }

        /* Input focus effect */
        .form-select:focus,
        .form-control:focus {
            box-shadow: 0 0 8px rgba(13, 110, 253, 0.5);
            border-color: #0d6efd;
            transition: box-shadow 0.3s ease, border-color 0.3s ease;
        }

        /* Slightly bolder modal title */
        .modal-title {
            font-weight: 600;
            font-size: 1.25rem;
        }
    </style>
@endpush
