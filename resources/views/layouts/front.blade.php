<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <!-- Character Encoding -->
    <meta charset="utf-8">

    <!-- Viewport Settings -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

    <!-- Metadata -->
    <meta name="organization" content="Agroinspeksiya">
    <meta name="author" content="Doston Olimov">
    <meta name="description" content="paxta.agroset.uz">

    <!-- Page Title -->
    <title>{{ trans('message.Page title') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="252x252" href="{{ asset('/resources/assets/images/paxta_logo.png') }}">
    <!-- Vendors styles-->

    <link rel="stylesheet" href="{{ asset('front/css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('front/css/main.css') }}" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="icon" href="{{ asset('img/logoNEW.png') }}" type="image/x-icon" />

    {{--    <!-- My style files --> --}}
    <link href="{{ asset('resources/assets/plugins/sweetalert2/sweetalert2.min.css') }}"
          rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ asset('build/css/bootstrap-datetimepicker.min.css') }}">
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
    @stack('styles') <!-- For Vue-specific styles -->
</head>

@if(in_array(Auth::User()->role, [\App\Models\User::ROLE_CITY_CHIGIT, \App\Models\User::ROLE_STATE_CHIGIT_BOSHLIQ, \App\Models\User::ROLE_STATE_CHIGI_XODIM]))
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
@else
    <div class="section" role="main">
        <div class="card">
            <div class="card-body text-center">
                <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp {{ trans('app.You Are Not Authorize This page.')}}</span>
            </div>
        </div>
    </div>
@endif
@include('front.layouts.footer')
<!-- JQUERY SCRIPTS JS -->
<script src="{{ asset('resources/assets/plugins/hyperform/dist/hyperform.js') }}"></script>
<script>hyperform(window)</script>
<script src="{{ asset('resources/assets/js/vendors/jquery-3.2.1.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/date-picker/jquery-ui.js') }}"></script>

<!-- Input Mask -->
<script src="{{ asset('resources/assets/plugins/input-mask/input-mask.min.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('resources/assets/plugins/select2/dist/js/select2.min.js') }}"></script>

<!-- Date Picker -->
<script src="{{ asset('resources/assets/plugins/date-picker/date-picker.js') }}"></script>

<!-- Bootstrap Bundle -->
<script src="{{ asset('resources/assets/js/vendors/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>

<!-- SweetAlert2 and Multi Select -->
<script src="{{ asset('resources/assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/multipleselect/multiple-select.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/multipleselect/multi-select.js') }}"></script>

<!-- Moment.js -->
<script src="{{ asset('resources/assets/js/moment.js') }}"></script>

<!-- Print Button -->
<script src="{{ asset('resources/assets/plugins/print/dist/jQuery.print.min.js') }}"></script>

<!-- Custom Scripts -->
<script src="{{ asset('resources/assets/js/custom.js') }}"></script>
<script src="{{ asset('resources/assets/js/myjs.js') }}"></script>

<!-- CoreUI and Necessary Plugins -->
<script src="{{ asset('/assets/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
<script src="{{ asset('/assets/vendors/simplebar/js/simplebar.min.js') }}"></script>

{{-- Data table js files--}}
<script src="{{ asset('resources/assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>

<script src="{{ asset('resources/assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/datatable/js//vfs_fonts.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/datatable/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/datatable/datatable.js') }}"></script>

@yield('scripts')
<!-- Vue-Specific Scripts -->
@stack('scripts') <!-- Push Vue-specific scripts here -->

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

</body>

</html>
