<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta name="description" content="Agroinspeksiya">
    <meta name="keywords"
          content="Agroinspeksiya, paxta tolasini sertifikatlashtirish, paxta tolasi,paxtani sertifikatlash">
    <meta name="author" content="Doston Olimov">
    <title>Paxta tolasini sertifikatlashtirish tizimi</title>

    <link rel="stylesheet" href="{{ URL::asset('front/css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ URL::asset('front/css/main.css') }}" type="text/css">

    <link rel="icon" href="{{ asset('img/logoNEW.png') }}" type="image/x-icon" />

    {{--    <!-- My style files --> --}}

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/color-style.css') }}">

    <link href="{{ URL::asset('resources/assets/plugins/tabs/tabs.css') }}" rel="stylesheet"/>

    <link rel="stylesheet" href="{{ URL::asset('resources/assets/fonts/fonts/font-awesome.min.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/myStyle.css') }}"/>

    @yield('styles')
</head>

<body class="app">
<!-- partial:partials/_sidebar.php -->

    @include('front.layouts.header')

<!-- partial -->
<main>
    <div class="container">
        @yield('content')
    </div>
</main>

@include('front.layouts.footer')
<!-- JQUERY SCRIPTS JS-->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>

<script src="{{ URL::asset('resources/assets/plugins/hyperform/dist/hyperform.js') }}"></script>
<script>
    hyperform(window)
</script>
<script src="{{ URL::asset('resources/assets/js/vendors/jquery-3.2.1.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/date-picker/jquery-ui.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/waves/js/popper.min.js') }}"></script>




<script src="{{ URL::asset('resources/assets/plugins/right-sidebar/right-sidebar.js') }}"></script>

<script src="{{ URL::asset('resources/assets/plugins/select2/dist/js/select2.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/date-picker/date-picker.js') }}"></script>
<script src="{{ URL::asset('resources/assets/js/vendors/bootstrap.bundle.min.js') }}"></script>

<script src="{{ URL::asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>

{{--    <!-- FILE UPLOADES JS -->--}}

<script sync type="text/javascript" src="{{ URL::asset('resources/assets/plugins/table-export/file-saver.min.js') }}">
</script>
<script sync type="text/javascript" src="{{ URL::asset('resources/assets/plugins/table-export/blob.min.js') }}">
</script>
<script src="{{ URL::asset('resources/assets/plugins/stickyTable/jquery.stickytable.js') }}"></script>
<script src="{{ URL::asset('resources/assets/js/moment.js') }}"></script>
<script src="{{ URL::asset('resources/assets/js/uz-latn.js') }}"></script>
<script src="{{ URL::asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<script src="/js/num.js"></script>
<script src="{{ URL::asset('resources/assets/plugins/print/dist/jQuery.print.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/cropper/cropper.min.js') }}"></script>
<script src="{{ URL::asset('build/js/control.js') }}"></script>
<script src="{{ URL::asset('resources/assets/js/custom.js') }}"></script>


@yield('scripts')


</body>

</html>
