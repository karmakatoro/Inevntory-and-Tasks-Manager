@extends('layouts.auth')

@section('title', 'Forgot Password - ' . env('APP_NAME'))

@section('content')
    <div class="col-md-8 col-lg-6 col-xl-4">
        <div class="card">

            <div class="card-body p-4">

                <div class="text-center w-75 m-auto">
                    <div class="auth-logo">
                        <a href="{{ route('login') }}" class="logo logo-dark text-center">
                            <span class="logo-lg">
                                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="22">
                            </span>
                        </a>

                        <a href="index.html" class="logo logo-light text-center">
                            <span class="logo-lg">
                                <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="22">
                            </span>
                        </a>
                    </div>
                    <p class="text-muted mb-4 mt-3">Enter your email address and we'll send you an email with instructions
                        to reset your password.</p>
                </div>

                <form action="#">

                    <div class="mb-3">
                        <label for="emailaddress" class="form-label">Email address</label>
                        <input class="form-control" type="email" id="emailaddress" required=""
                            placeholder="Enter your email">
                    </div>

                    <div class="d-grid text-center">
                        <button class="btn btn-primary" type="submit"> Reset Password </button>
                    </div>

                </form>

            </div> <!-- end card-body -->
        </div>
        <!-- end card -->

        <div class="row mt-3">
            <div class="col-12 text-center">
                <p class="text-muted">Back to <a href="auth-login.html" class="text-primary fw-medium ms-1">Log in</a></p>
            </div> <!-- end col -->
        </div>
        <!-- end row -->

    </div> <!-- end col -->
@endsection
