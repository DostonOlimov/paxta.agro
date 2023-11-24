<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ URL::asset('resources/assets/images/paxta_logo.png') }}" type="image/x-icon"/>
    <title>Paxta tolasini sertifikatlash tizimi</title>

    <link href="{{ URL::asset('vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('resources/assets/css/color-style.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('resources/assets/plugins/p-scroll/p-scroll.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/myStyle.css') }}">
    <link href="{{ URL::asset('resources/assets/plugins/single-page/css/single-page.css') }}"
          rel="stylesheet" type="text/css">
    <script src="{{ URL::asset('resources/assets/js/vendors/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ URL::asset('resources/assets/js/vendors/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ URL::asset('build/js/control.js') }}"></script>
    <script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>
    <style>
        .help-block {
            text-align: left;
        }
        .page{
            background-image: url("{{ URL::asset('/img/paxta_back2.jpeg')}}");
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            height: 100%;
        }
    </style>
</head>

<body class="app" >
<div class="login-img">
    <div id="global-loader">
        <img src="{{ URL::asset('/img/paxta_back.jpg')}}" class="loader-img"
             alt="Loader">
    </div>

    <div class="page">
        <div class="">
            <div class="container-login100">
                <div class="wrap-login100 p-6">
                    <div class="col col-login mx-auto mb-4">
                        <div class="text-center">
                            <img style="width: 200px; height: auto;" src="{{ url('img/logoNEW.png')}}"
                                 class="header-brand-img" alt="logo">
                        </div>
                    </div>
                        <form action="{{ route('login') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <b> <label class="label">Email</label></b>
                                <div class="input-group">
                                    <input name="email" type="text" class="form-control" placeholder="Email" value="{{ old('email') }}">
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
                                    <input name="password" type="password" class="form-control" placeholder="Parol" >
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                          <i class="mdi mdi-check-circle-outline"></i>
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
</script>
</html>
