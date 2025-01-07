@extends('layouts.app')

@section('content')
    <div id="app"></div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('js/app.js') }}"></script>
@endpush
