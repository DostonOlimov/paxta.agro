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

    <!-- Sidebar Styles -->
    <link rel="stylesheet" href="{{ asset('/assets/vendors/simplebar/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/vendors/simplebar.css') }}">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <!-- SweetAlert2 Stylesheet -->
    <link rel="stylesheet" href="{{ asset('resources/assets/plugins/sweetalert2/sweetalert2.min.css') }}">

    <!-- DatePicker Stylesheet -->
    <link rel="stylesheet" href="{{ asset('build/css/bootstrap-datetimepicker.min.css') }}">

    <!-- Custom Stylesheets -->
    <link rel="stylesheet" href="{{ asset('resources/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/assets/css/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/assets/css/color-style.css') }}">
    <!-- Select2 css files -->
    <link rel="stylesheet" href="{{ asset('resources/assets/plugins/select2/dist/css/select2.min.css') }}">
    <!-- Table css file -->
    <link rel="stylesheet" href="{{ asset('resources/assets/plugins/tabs/tabs.css') }}">
    <!-- Font awesome min css file -->
    <link rel="stylesheet" href="{{ asset('resources/assets/fonts/fonts/font-awesome.min.css') }}">
    <!-- My style file -->
    <link rel="stylesheet" href="{{ asset('resources/assets/css/myStyle.css') }}">

    @php
        // Define style settings for different 'crop' values
        $styles = [
            2 => [
                'header' => '#343a40',
                'sidebar' => '#495057',
                'navgroupBg' => '#797f84',
                'activeLinkBg' => 'var(--cui-sidebar-nav-link-active-bg)',
                'hoverLinkBg' => 'var(--cui-sidebar-nav-link-hover-bg)',
                'stickyHeader' => '#d4d4d4',
            ],
            3 => [
                'header' => '#157347',
                'sidebar' => '#198754',
                'navgroupBg' => '#4eba69',
                'activeLinkBg' => '#1cbd53',
                'hoverLinkBg' => '#1cbd53',
                'stickyHeader' => '#d9f1e4',
            ],
            'default' => [
                'header' => '#0E46A3',
                'sidebar' => '#0d6efd',
                'navgroupBg' => '#4eb6ba',
                'activeLinkBg' => '#31a5f1',
                'hoverLinkBg' => '#31a5f1',
                'stickyHeader' => '#b9e6f1',
            ],
        ];

        // Get the current crop from session, or default if not set
        $crop = session('crop', 'default');
        $currentStyles = $styles[$crop] ?? $styles['default'];
    @endphp

    <style>
        .page-header {
            background-color: {{ $currentStyles['header'] }};
        }
        .sidebar-toggler,
        .sidebar-brand,
        #myBtn {
            background-color: {{ $currentStyles['header'] }};
        }
        .sidebar-nav {
            background-color: {{ $currentStyles['sidebar'] }};
        }
        .sidebar-nav .nav-link.active1 {
            color: var(--cui-sidebar-nav-link-active-color);
            background: {{ $currentStyles['activeLinkBg'] }} !important;
        }
        .sidebar-nav .nav-link:hover {
            color: var(--cui-sidebar-nav-link-hover-color);
            text-decoration: none;
            background: {{ $currentStyles['hoverLinkBg'] }};
        }
        .nav-group .nav-group-items {
            background-color: {{ $currentStyles['navgroupBg'] }};
        }
        .header-sticky {
            background-color: {{ $currentStyles['stickyHeader'] }};
        }
    </style>

@yield('styles')
@stack('styles') <!-- For Vue-specific styles -->
</head>
<body class="app">
@php $settings = settings(); @endphp

@if(!in_array(Auth::User()->role, [\App\Models\User::ROLE_CITY_CHIGIT, \App\Models\User::ROLE_STATE_CHIGIT_BOSHLIQ, \App\Models\User::ROLE_STATE_CHIGI_XODIM]))
    <!-- Sidebar and Navbar -->
    @include('layouts.blocks.sidebar')
    <div class="wrapper d-flex flex-column min-vh-100 bg-light" style="padding-right: 0!important;">
        @include('layouts.blocks.navbar')
        <div class="body flex-grow-1 px-3">
            @yield('content')
        </div>
    </div>
@else
    <!-- Unauthorized Access Message -->
    <div class="section" role="main">
        <div class="card">
            <div class="card-body text-center">
                    <span class="titleup text-danger">
                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;
                        {{ trans('app.You Are Not Authorized To Access This Page.') }}
                    </span>
            </div>
        </div>
    </div>
@endif

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

<!-- Vue-Specific Scripts -->
@stack('scripts') <!-- Push Vue-specific scripts here -->

@yield('scripts')

<script src="{{ asset('js/languageChange.js') }}"></script>
<script>
    function changeLanguage(language) {
        var token = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: '/change-language',
            data: { language: language, _token: token },
            success: function (data) {
                location.reload();
            },
            error: function (error) {
                console.error('Error changing language', error);
            }
        });
    }

    // Function to change year
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

    // Function to change crop
    function changeCrop(year) {
        $.ajax({
            type: 'POST',
            url: '/change-crop',
            data: { crop: year, _token: "{{ csrf_token() }}" },
            success: function () {
                window.location.href = '/home';
            },
            error: function (error) {
                console.error('Error changing crop', error);
            }
        });
    }
</script>
</body>
</html>
