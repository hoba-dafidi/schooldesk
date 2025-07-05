@extends('layouts.blankLayout')
@section('title', 'Verify Email')
@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection
@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card px-sm-6 px-0">
                    <div class="card-body">
                        <h4 class="mb-1">Verify Your Email 📧</h4>
                        <p>Thanks for signing up! Please verify your email address by clicking on the link we just emailed
                            to you.</p>
                        <form method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary d-grid w-100">Resend Verification Email</button>
                        </form>
                        <p class="text-center mt-3">
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                        </p>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
