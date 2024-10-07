@extends('layouts.auth')

@section('title', 'Login - ' . env('APP_NAME'))

@section('content')
    <div class="col-md-8 col-lg-6 col-xl-4">
        <div class="card">

            <div class="card-body p-4">

                <div class="text-center w-75 m-auto">
                    <div class="auth-logo">
                        <a href="index.html" class="logo logo-dark text-center">
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
                    <p class="text-muted mb-4 mt-3">Enter your email address and password to access admin
                        panel.</p>
                </div>

                <form action="{{ route('auth.get-connected') }}" method="POST" id="requestLogin">
                    @csrf
                    <div class="mb-2">
                        <label for="emailaddress" class="form-label">Email address</label>
                        <input class="form-control" type="email" name="email" id="emailaddress" required=""
                            placeholder="Enter your email">
                    </div>

                    <div class="mb-2">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group input-group-merge">
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Enter your password">

                            <div class="input-group-text" data-password="false">
                                <span class="password-eye"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" name="rememberMe" type="checkbox" id="checkbox-signin" checked>
                            <label class="form-check-label" for="checkbox-signin">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <div class="d-grid mb-0 text-center">
                        <button class="btn btn-primary" type="submit"> Log In </button>
                    </div>

                </form>
            </div> <!-- end card-body -->
        </div>
        <!-- end card -->

        <div class="row mt-3">
            <div class="col-12 text-center">
                <p> <a href="{{ route('auth.forgot') }}" class="text-muted ms-1">Forgot your password?</a></p>
            </div> <!-- end col -->
        </div>
        <!-- end row -->

    </div> <!-- end col -->
@endsection
