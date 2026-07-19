<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ url('/assets/img/logo.png') }}" type="image/x-icon">
    @include('components.meta', ['title' => 'Error 503: Service Unavailable'])
    @include('components.style')
    <style>
        .error-img {
            width: 400px;
        }
        .back:hover span {
            text-decoration: underline !important;
        }
        .message {
            margin-top: -50px;
        }
        @media (max-width: 460px) {
            .error-img {
                width: 300px;
            }
            .message {
                margin-top: 0px;
            }
        }
    </style>
</head>
<body style="background-color: #EAF6F1 !important;">
    <div class="gap-1 position-absolute top-50 start-50 translate-middle d-flex flex-column align-items-center justify-content-center w-100">
        <img src="{{ url('assets/img/503.gif') }}" class="error-img" alt="Error gif">
        <div class="container message d-flex flex-column">
            <h3 class="text-center fw-bold">Oopss..</h3>
            <span class="text-center">This website is currently under maintenance. We will be back soon!</span>
        </div>
    </div>
    @include('components.script')
</body>
</html>
