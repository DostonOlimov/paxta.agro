<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta name="description" content="paxta.agroset">
    <meta name="author" content="Doston Olimov">
    <title>Paxta tolasini sertifikatlashtirish tizimi</title>
    <link rel="icon" type="image/png" sizes="252x252" href="{{ asset('/resources/assets/images/paxta_logo.png') }}">
    <!-- Vendors styles-->
     <link rel="stylesheet" href="{{ asset('/assets/vendors/simplebar/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/vendors/simplebar.css') }}">
    <link href="{{ asset('/assets/css/style.css') }}" rel="stylesheet">

    <!-- My style files -->
    <link href="{{ asset('resources/assets/plugins/sweetalert2/sweetalert2.min.css') }}"
          rel="stylesheet" type="text/css">
    <link rel="stylesheet"
          href="{{ asset('resources/assets/plugins/print/demo/css/normalize.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('resources/assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('resources/assets/css/skins-modes.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('resources/assets/plugins/sidemenu/sidemenu.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('resources/assets/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('resources/assets/plugins/p-scroll/p-scroll.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('resources/assets/css/icons.css') }}">

    <link rel="stylesheet" type="text/css"
          href="{{ asset('resources/assets/plugins/cropper/cropper.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('build/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('resources/assets/css/color-style.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('resources/assets/plugins/select2/dist/css/select2.min.css') }}">
    <link href="{{ asset('resources/assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}"
          rel="stylesheet"/>
    <link rel="stylesheet"
          href="{{ asset('resources/assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}">
    <link href="{{ asset('resources/assets/plugins/datatable/responsive.bootstrap4.min.css') }}"
          rel="stylesheet"/>
    <link rel="stylesheet"
          href="{{ asset('resources/assets/plugins/hyperform/css/hyperform.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/assets/css/style.css') }}"/>

    <link href="{{ asset('resources/assets/plugins/tabs/tabs.css') }}" rel="stylesheet"/>

    <link rel="stylesheet" href="{{ asset('resources/assets/fonts/fonts/font-awesome.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('resources/assets/css/myStyle.css') }}"/>
    @php
        $styles = [
            2 => [
                'header' => '#343a40',
                'sidebar' => '#495057',
                'navgroupBg' => '#797f84',
                'activeLinkBg' => 'var(--cui-sidebar-nav-link-active-bg)',
                'hoverLinkBg' => 'var(--cui-sidebar-nav-link-hover-bg)',
                'stickyHeader' => '#d4d4d4'
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

        $crop = session('crop');
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
        .nav-group .nav-group-items{
            background-color: {{ $currentStyles['navgroupBg'] }};
        }
        .header-sticky {
                background-color: {{ $currentStyles['stickyHeader'] }};
            }
    </style>
    @yield('styles')
</head>
<body class="app">
<?php
$settings = settings();
?>
@if(!(in_array(Auth::User()->role, [\App\Models\User::ROLE_CITY_CHIGIT, \App\Models\User::ROLE_STATE_CHIGIT_BOSHLIQ, \App\Models\User::ROLE_STATE_CHIGI_XODIM])))
<!-- partial:partials/_sidebar.php -->
@include('layouts.blocks.sidebar')
<!-- partial -->

<div class="wrapper d-flex flex-column min-vh-100 bg-light" style="padding-right: 0!important;">
    <!-- partial:partials/_navbar.php -->
    @include('layouts.blocks.navbar')
    <!-- partial -->
    <div class="body flex-grow-1 px-3">
            @yield('content')
    </div>
    <!-- page-body-wrapper ends -->
</div>
@else
    <div class="section" role="main">
        <div class="card">
            <div class="card-body text-center">
                <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp {{ trans('app.You Are Not Authorize This page.')}}</span>
            </div>
        </div>
    </div>
@endif
<!-- JQUERY SCRIPTS JS-->
{{--<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>--}}

<script src="{{ asset('resources/assets/plugins/hyperform/dist/hyperform.js') }}"></script>
<script>hyperform(window)</script>
<script src="{{ asset('resources/assets/js/vendors/jquery-3.2.1.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/date-picker/jquery-ui.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/waves/js/popper.min.js')}}"></script>
<script src="/resources/assets/plugins/tabs/jquery.multipurpose_tabcontent.js"></script>

<script src="{{ asset('resources/assets/plugins/input-mask/input-mask.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/p-scroll/p-scroll.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/sidemenu/sidemenu.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/sidemenu-responsive-tabs/js/sidemenu-responsive-tabs.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/tabs/jquery.multipurpose_tabcontent.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/tabs/tab-content.js') }}"></script>
<script src="{{ asset('resources/assets/js/left-menu.js') }}"></script>



<script src="{{ asset('resources/assets/plugins/right-sidebar/right-sidebar.js') }}"></script>

<script src="{{ asset('resources/assets/plugins/select2/dist/js/select2.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/date-picker/date-picker.js') }}"></script>
<script src="{{ asset('resources/assets/js/vendors/bootstrap.bundle.min.js') }}"></script>

<script src="{{ asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>

<!-- FILE UPLOADES JS -->
<script src="{{ asset('resources/assets/plugins/fileupload/js/fileupload.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/fileupload/js/file-upload.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/multipleselect/multiple-select.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/multipleselect/multi-select.js') }}"></script>

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

<script sync type="text/javascript" src="{{ asset('resources/assets/plugins/table-export/file-saver.min.js') }}"></script>
<script sync type="text/javascript" src="{{ asset('resources/assets/plugins/table-export/blob.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/stickyTable/jquery.stickytable.js') }}"></script>
<script src="{{ asset('resources/assets/js/moment.js') }}"></script>
<script src="{{ asset('resources/assets/js/uz-latn.js') }}"></script>
<script src="{{ asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

{{--<script src="/js/num.js"></script>--}}
<script src="{{ asset('resources/assets/plugins/print/dist/jQuery.print.min.js') }}"></script>
<script src="{{ asset('resources/assets/plugins/cropper/cropper.min.js') }}"></script>
<script src="{{ asset('build/js/control.js') }}"></script>

<script src="{{ asset('resources/assets/js/custom.js') }}"></script>
<script src="{{ asset('resources/assets/js/myjs.js') }}"></script>

<!-- CoreUI and necessary plugins-->
<script src="{{ asset('/assets/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
<script src="{{ asset('/assets/vendors/simplebar/js/simplebar.min.js') }}"></script>
<!-- Plugins and scripts required by this view-->
<script src="{{ asset('/assets/vendors/@coreui/chartjs/js/coreui-chartjs.js') }}"></script>

<script src="{{ asset('/assets/vendors/@coreui/utils/js/coreui-utils.js') }}"></script>
@yield('scripts')
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
    function changeCrop(year) {
        $.ajax({
            type: 'POST',
            url: '/change-crop',
            data: { crop: year, _token: "{{ csrf_token() }}" },
            success: function () {
                window.location.href = '/home';
            },
            error: function (error) {
                console.error('Error changing year', error);
            }
        });
    }
</script>
<script>
    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the button that opens the modal
    var btn = document.getElementById("myBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        moment().format();
        $('.card-body').removeClass('p-6');
        $('#datatable-1_length label').css('visibility', 'hidden');
        $('#datatable-1_wrapper #datatable-1_info').css('visibility', 'hidden');
        $('#datatable-1_wrapper #datatable-1_paginate').css('visibility', 'hidden');
        $('#example-3_length label,#example-3_wrapper #example-3_info, #example-3_wrapper #example-3_paginate, #example-3_wrapper #example-3_filter').hide();

        $('select, input[type!="password"]').attr("autocomplete", "off").attr("title", "");
        $('input, select').attr("data-pattern-mismatch", "Kerakli shaklda to'ldiring").attr("data-original-title", "Maydonni to'ldiring");
        $('input[type="checkbox"]').attr("data-pattern-mismatch", "").attr("data-value-missing", "");
    })
</script>

</body>
</html>
