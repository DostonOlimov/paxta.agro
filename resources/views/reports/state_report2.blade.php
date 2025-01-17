@extends('layouts.front')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/sertificate.css') }}" type="text/css">

    <!-- page content -->
    <div class="section" style="margin: 140px 0">
        <!-- PAGE-HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i> Sifat sertifikati bo'yicha arizalar ro'yxati
                </li>
            </ol>
        </div>
        <div id="app">
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('js/app.js') }}"></script>
@endpush
