<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ url('/assets/img/logo.png') }}" type="image/x-icon">
    @include('components.meta', ['title' => 'Error 403: Access Forbidden'])
    @include('components.style')
    <style>
        .error-img {
            width: 400px;
        }
        .back:hover span {
            text-decoration: underline !important;
        }
        @media (max-width: 460px) {
            .error-img {
                width: 300px;
            }
        }
    </style>
</head>
<body style="background-color: #EAF6F1 !important;">
    <nav class="px-2 py-3 container-fluid px-md-5">
        <a href="{{ route('dashboard') }}" class="gap-1 back d-flex align-items-center text-decoration-none text-dark fw-bold">
            <i class="bx bx-chevron-left fs-3 fw-bold"></i>
            <span>Back to Home</span>
        </a>
    </nav>
    <div class="gap-1 position-absolute top-50 start-50 translate-middle d-flex flex-column align-items-center justify-content-center w-100">
        <img src="{{ url('assets/img/403.gif') }}" class="error-img" alt="Error gif">
        <div class="container message d-flex flex-column">
            <h3 class="text-center fw-bold">Access Forbidden.</h3>
            <span class="text-center">You don't have permission to perform this action or view this page!</span>
        </div>
    </div>
    @include('components.script')
</body>
</html>
