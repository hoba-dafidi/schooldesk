@extends('layouts/contentNavbarLayout')

@section('title', 'Create Ticket')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow rounded-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Submit a New Ticket</h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('user.tickets.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="subject" class="form-label fw-semibold">Subject</label>
                                <input type="text" id="subject" name="subject" class="form-control"
                                    value="{{ old('subject') }}" required>
                                @error('subject')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="priority" class="form-label fw-semibold">Priority</label>
                                <select id="priority" name="priority" class="form-select" required>
                                    <option value="" disabled selected>Select Priority</option>
                                    @foreach (\App\Enums\TicketPriority::cases() as $priority)
                                        <option value="{{ $priority->value }}"
                                            {{ old('priority') === $priority->value ? 'selected' : '' }}>
                                            {{ $priority->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-send me-1"></i> Submit Ticket
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
