<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta name="description" content="paxta.agroset">
    <meta name="author" content="Doston Olimov">
    <title>Paxta tolasini sertifikatlashtirish tizimi</title>
    <link rel="icon" type="image/png" sizes="252x252" href="{{ URL::asset('/resources/assets/images/paxta_logo.png') }}">

    <!-- Vendors styles-->
     <link rel="stylesheet" href="{{ URL::asset('/assets/vendors/simplebar/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('/assets/css/vendors/simplebar.css') }}">
    <link href="{{ URL::asset('/assets/css/style.css') }}" rel="stylesheet">

    <!-- My style files -->
    <link href="{{ URL::asset('resources/assets/plugins/sweetalert2/sweetalert2.min.css') }}"
          rel="stylesheet" type="text/css">
    <link rel="stylesheet"
          href="{{ URL::asset('resources/assets/plugins/print/demo/css/normalize.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('resources/assets/css/skins-modes.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('resources/assets/plugins/sidemenu/sidemenu.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('resources/assets/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('resources/assets/plugins/p-scroll/p-scroll.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/icons.css') }}">

    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('resources/assets/plugins/cropper/cropper.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('build/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('resources/assets/css/color-style.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('resources/assets/plugins/select2/dist/css/select2.min.css') }}">
    <link href="{{ URL::asset('resources/assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}"
          rel="stylesheet"/>
    <link rel="stylesheet"
          href="{{ URL::asset('resources/assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}">
    <link href="{{ URL::asset('resources/assets/plugins/datatable/responsive.bootstrap4.min.css') }}"
          rel="stylesheet"/>
    <link rel="stylesheet"
          href="{{ URL::asset('resources/assets/plugins/hyperform/css/hyperform.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/style.css') }}"/>

    <link href="{{ URL::asset('resources/assets/plugins/tabs/tabs.css') }}" rel="stylesheet"/>

    <link rel="stylesheet" href="{{ URL::asset('resources/assets/fonts/fonts/font-awesome.min.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/myStyle.css') }}"/>
    @yield('styles')
</head>
<body class="app">
<?php
$userid = Auth::User()->id;
$settings = settings();
?>
@if(Auth::User()->role != \App\Models\User::ROLE_CUSTOMER)
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
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>

<script src="{{ URL::asset('resources/assets/plugins/hyperform/dist/hyperform.js') }}"></script>
<script>hyperform(window)</script>
<script src="{{ URL::asset('resources/assets/js/vendors/jquery-3.2.1.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/date-picker/jquery-ui.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/waves/js/popper.min.js')}}"></script>
<script src="/resources/assets/plugins/tabs/jquery.multipurpose_tabcontent.js"></script>

<script src="{{ URL::asset('resources/assets/plugins/input-mask/input-mask.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/p-scroll/p-scroll.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/sidemenu/sidemenu.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/sidemenu-responsive-tabs/js/sidemenu-responsive-tabs.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/tabs/jquery.multipurpose_tabcontent.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/tabs/tab-content.js') }}"></script>
<script src="{{ URL::asset('resources/assets/js/left-menu.js') }}"></script>



<script src="{{ URL::asset('resources/assets/plugins/right-sidebar/right-sidebar.js') }}"></script>

<script src="{{ URL::asset('resources/assets/plugins/select2/dist/js/select2.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/date-picker/date-picker.js') }}"></script>
<script src="{{ URL::asset('resources/assets/js/vendors/bootstrap.bundle.min.js') }}"></script>

<script src="{{ URL::asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>

<!-- FILE UPLOADES JS -->
<script src="{{ URL::asset('resources/assets/plugins/fileupload/js/fileupload.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/fileupload/js/file-upload.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/multipleselect/multiple-select.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/multipleselect/multi-select.js') }}"></script>

<script src="{{ URL::asset('resources/assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>

<script src="{{ URL::asset('resources/assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/datatable/js//vfs_fonts.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/datatable/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/datatable/datatable.js') }}"></script>
<script sync type="text/javascript" src="{{ URL::asset('resources/assets/plugins/table-export/file-saver.min.js') }}"></script>
<script sync type="text/javascript" src="{{ URL::asset('resources/assets/plugins/table-export/blob.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/stickyTable/jquery.stickytable.js') }}"></script>
<script src="{{ URL::asset('resources/assets/js/moment.js') }}"></script>
<script src="{{ URL::asset('resources/assets/js/uz-latn.js') }}"></script>
<script src="{{ URL::asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

{{--<script src="/js/num.js"></script>--}}
<script src="{{ URL::asset('resources/assets/plugins/print/dist/jQuery.print.min.js') }}"></script>
<script src="{{ URL::asset('resources/assets/plugins/cropper/cropper.min.js') }}"></script>
<script src="{{ URL::asset('build/js/control.js') }}"></script>
<script src="{{ URL::asset('resources/assets/js/custom.js') }}"></script>
<script src="{{ URL::asset('resources/assets/js/myjs.js') }}"></script>

<!-- CoreUI and necessary plugins-->
<script src="{{ URL::asset('/assets/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
<script src="{{ URL::asset('/assets/vendors/simplebar/js/simplebar.min.js') }}"></script>
<!-- Plugins and scripts required by this view-->
<script src="{{ URL::asset('/assets/vendors/@coreui/chartjs/js/coreui-chartjs.js') }}"></script>

<script src="{{ URL::asset('/assets/vendors/@coreui/utils/js/coreui-utils.js') }}"></script>
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
</script>

</body>
</html>
