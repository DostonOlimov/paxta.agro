<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta name="description" content="paxta.agroset">
    <meta name="author" content="Doston Olimov">
    <title>Paxta tolasini sertifikatlashtirish tizimi</title>
    <link rel="icon" type="image/png" sizes="252x252" href="{{ asset('/resources/assets/images/paxta_logo.png') }}">
    <!-- Vendors styles-->

    <link rel="stylesheet" href="{{ asset('front/css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('front/css/main.css') }}" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="icon" href="{{ asset('img/logoNEW.png') }}" type="image/x-icon" />

    {{--    <!-- My style files --> --}}
    <link href="{{ asset('resources/assets/plugins/sweetalert2/sweetalert2.min.css') }}"
          rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('resources/assets/plugins/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('resources/assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('resources/assets/css/color-style.css') }}">

    <link href="{{ asset('resources/assets/plugins/tabs/tabs.css') }}" rel="stylesheet"/>

    <link rel="stylesheet" href="{{ asset('resources/assets/fonts/fonts/font-awesome.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('resources/assets/css/myStyle.css') }}"/>
    <style>
        .hf-warning{
            color:red !important;
        }
    </style>
    @yield('styles')
</head>

<body class="app">
<!-- partial:partials/_sidebar.php -->
<nav>
    @include('front.layouts.header')
</nav>
<!-- partial -->
<main>
    <div class="container">
        @yield('content')
    </div>
</main>
@include('front.layouts.footer')
<!-- JQUERY SCRIPTS JS-->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>

<script src="{{ asset('resources/assets/plugins/hyperform/dist/hyperform.js') }}"></script>
<script>
    hyperform(window)
</script>
<script src="{{ asset('resources/assets/js/vendors/jquery-3.2.1.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/date-picker/jquery-ui.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/waves/js/popper.min.js') }}"></script>




<script src="{{ asset('resources/assets/plugins/right-sidebar/right-sidebar.js') }}"></script>

<script src="{{ asset('resources/assets/plugins/select2/dist/js/select2.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/date-picker/date-picker.js') }}"></script>
<script src="{{ asset('resources/assets/js/vendors/bootstrap.bundle.min.js') }}"></script>

<script src="{{ asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>

{{--    <!-- FILE UPLOADES JS -->--}}

<script sync type="text/javascript" src="{{ asset('resources/assets/plugins/table-export/file-saver.min.js') }}">
</script>
<script sync type="text/javascript" src="{{ asset('resources/assets/plugins/table-export/blob.min.js') }}">
</script>
<script src="{{ asset('resources/assets/plugins/stickyTable/jquery.stickytable.js') }}"></script>
<script src="{{ asset('resources/assets/js/moment.js') }}"></script>
<script src="{{ asset('resources/assets/js/uz-latn.js') }}"></script>
<script src="{{ asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<script src="/js/num.js"></script>
<script src="{{ asset('resources/assets/plugins/print/dist/jQuery.print.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/cropper/cropper.min.js') }}"></script>
<script src="{{ asset('build/js/control.js') }}"></script>
<script src="{{ asset('resources/assets/js/custom.js') }}"></script>
<script>
    function changeLanguage(language) {
        // Use AJAX to send a request to change the language
        var token = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: '/change-language',
            data: { language: language , _token: token},
            success: function (data) {
                location.reload();
            },
            error: function (error) {
                console.error('Error changing language', error);
            }
        });
    }

    function changeYear(year) {
        $.ajax({
            type: 'POST',
            url: '/change-year',
            data: { year: year, _token: "{{ csrf_token() }}" },
            success: function () {
                location.reload();
            },
            error: function (error) {
                console.error('Error changing year', error);
            }
        });
    }
</script>

@yield('scripts')


</body>

</html>
