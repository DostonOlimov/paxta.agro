<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ URL::asset('resources/assets/images/paxta_logo.png') }}" type="image/x-icon" />
    <title>Paxta tolasini sertifikatlash tizimi</title>

    <link href="{{ URL::asset('vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/color-style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/plugins/p-scroll/p-scroll.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/myStyle.css') }}">
    <link href="{{ URL::asset('resources/assets/plugins/single-page/css/single-page.css') }}" rel="stylesheet"
        type="text/css">
    <script src="{{ URL::asset('resources/assets/js/vendors/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ URL::asset('resources/assets/js/vendors/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ URL::asset('build/js/control.js') }}"></script>
    <script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>
    <style>
        .help-block {
            display: block;
            width: 100%;
            color: red;
            font-weight: 700;
            margin: -8px 0 15px 0;
            font-size: 14px;
            text-align: center;
        }

        body {
            overflow: hidden;
        }

        .page {
            background: linear-gradient(180deg,
                    #000000 -0.45%,
                    rgba(0, 0, 0, 0.6) -0.69%,
                    rgba(0, 0, 0, 0.4) 100%,
                    rgba(255, 255, 255, 0.3) 100%) center/cover no-repeat,
                url("{{ URL::asset('/img/paxta_back2.jpeg') }}") center/cover no-repeat,
                center/cover no-repeat #d3d3d3;

            height: 100vh;
        }

        .input-group-append span i {
            font-size: 20px
        }

        .input-group-text {
            cursor: pointer;
        }

        strong {}
    </style>
</head>

<body class="app">
    <div class="login-img">
        <div id="global-loader">
            <img src="{{ URL::asset('/img/paxta_back.jpg') }}" class="loader-img" alt="Loader">
        </div>

        <div class="page">
            <div class="">
                <div class="container-login100">
                    <div class="wrap-login100 p-6">
                        <div class="col col-login mx-auto mb-4">
                            <div class="text-center">
                                <img style="width: 200px; height: auto;  filter: drop-shadow(0px 3px 6px rgba(0, 0, 0, 0.47));" src="{{ url('img/logoNEW.png') }}"
                                    class="header-brand-img" alt="logo">
                            </div>
                        </div>
                        <form action="{{ route('login') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <b> <label class="label">Email</label></b>
                                <div class="input-group">
                                    <input name="email" type="text" class="form-control" placeholder="Email"
                                        value="{{ old('email') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="mdi mdi-check-circle-outline"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>Kiritilgan pochta ma'lumotlari noto'g'ri</strong>
                                </span>
                            @endif
                            <div class="form-group">
                                <b><label class="label">Password</label></b>
                                <div class="input-group">
                                    <input id="password-input" name="password" type="password" class="form-control"
                                        placeholder="Parol">
                                    <div class="input-group-append">
                                        <span id="password-toggle" class="input-group-text">
                                            <i id="eye-icon" class="mdi mdi-eye-off"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>Parol kiritilmagan yoki noto'g'ri kiritilgan</strong>
                                </span>
                            @endif
                            <div class="form-group">
                                <button class="btn btn-primary submit-btn btn-block">Kirish</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    $(document).ready(() => $("#global-loader").fadeOut())

    const passwordInput = document.getElementById('password-input');
    const passwordToggle = document.getElementById('password-toggle');
    const eyeIcon = document.getElementById('eye-icon');

    passwordToggle.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('mdi-eye-off');
            eyeIcon.classList.add('mdi-eye');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('mdi-eye');
            eyeIcon.classList.add('mdi-eye-off');
        }
    });
</script>

</html>
