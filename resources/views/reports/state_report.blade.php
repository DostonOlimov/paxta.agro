@extends('layouts.app')

@section('content')
    <div class="page-header">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fe fe-life-buoy mr-1"></i>&nbsp {{ trans("message.Hududlar kesimda ma'lumot") }}
            </li>
        </ol>
    </div>
    <div id="app">
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="{{ mix('js/app.js') }}"></script>
@endpush
