@extends('layouts.auth', ['title' => 'Register - Finance Tracker'])

@section('content')
    <div class="row w-100" style="height: 100svh;">
        <div class="col col-12 col-md-6 col-lg-7 d-flex flex-column justify-content-center" id="hero">
            <a href="" class="gap-2 logo d-flex align-items-center justify-content-start ms-5 text-decoration-none">
                <img src="{{ url('assets/img/logo.png') }}" alt="Logo" style="width: 30px; height: auto;">
                <h4 class="py-0 my-0 text-center text-color fw-bold">Finance Tracker</h4>
            </a>
            <div class="d-flex justify-content-center">
                <img src="{{ url('assets/img/hero2.gif') }}" alt="Register" style="width: 80%; height: auto;">
            </div>
        </div>
        <div class="col col-12 col-sm-12 col-md-6 col-lg-5 d-flex flex-column justify-content-center">
            <div class="d-flex flex-column justify-content-between h-100">
                <div class="container d-flex flex-column justify-content-center px-auto px-md-5 h-100">
                    <div class="d-flex flex-column align-items-center mt-lg-4">
                        <a href="" class="mb-4 d-flex flex-column align-items-center d-none text-decoration-none" id="logo-mobile">
                            <img src="{{ url('assets/img/logo.png') }}" alt="Logo"
                                style="width: 80px; height: auto;">
                        </a>
                        <h3 class="fw-bold">Register</h3>
                        <p>Finance Tracker</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="mt-4 auth">
                        @csrf

                        <div class="mb-3 content">
                            <div class="pass-logo">
                                <i class='bx bx-user'></i>
                            </div>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" placeholder="Full Name"
                                value="{{ old('name') }}" required autocomplete="name" autofocus>

                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 content">
                            <div class="pass-logo">
                                <i class='bx bx-envelope'></i>
                            </div>
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                                value="{{ old('email') }}" required autocomplete="email">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 content">
                            <div class="pass-logo">
                                <i class='bx bx-lock-alt'></i>
                            </div>
                            <div class="d-flex align-items-center position-relative">
                                <input type="password" id="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    style="padding-right: 45px;" placeholder="Password" required>
                                <div class="showPass d-flex align-items-center justify-content-center position-absolute end-0 h-100"
                                    id="showPass" style="cursor: pointer; width: 50px; border-radius: 0px 10px 10px 0px;"
                                    onclick="showPass()">
                                    <i class="fa-regular fa-eye-slash"></i>
                                </div>
                            </div>

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 content">
                            <div class="pass-logo">
                                <i class='bx bx-lock-alt'></i>
                            </div>
                            <div class="d-flex align-items-center position-relative">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    style="padding-right: 45px;" placeholder="Confirm Password" required>
                                <div class="showPass d-flex align-items-center justify-content-center position-absolute end-0 h-100"
                                    id="showPass2" style="cursor: pointer; width: 50px; border-radius: 0px 10px 10px 0px;"
                                    onclick="showPass2()">
                                    <i class="fa-regular fa-eye-slash"></i>
                                </div>
                            </div>

                            @error('password_confirmation')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="gap-1 mt-4 d-grid">
                            <button class="btn btn-primary d-block w-100 fw-semibold" type="submit">Register</button>
                            <span class="py-0 mx-auto my-0 text-secondary d-block">or</span>
                            <a href="{{ route('google.redirect') }}"
                                class="btn btn-light border-success color-dark text-dark rounded-3 w-100 fw-semibold">
                                <img src="{{ url('assets/img/google-icon.png') }}" style="width: 20px;"
                                    alt="Google Icon">
                                Continue with Google
                            </a>
                        </div>
                        <p class="mt-2 mb-0 text-center text-secondary">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-decoration-underline">Login</a>
                        </p>
                    </form>
                </div>
                <div class="py-5 footer d-flex justify-content-center" style="height: 20px">
                    <small class="text-secondary">Copyright &copy;{{ date('Y') }} <a href="https://hikmalfalah.page.gd" class="fs-7" target="_blank">Hikmal Falah</a>. All rights reserved.</small>
                </div>
            </div>
        </div>
    </div>
@endsection
