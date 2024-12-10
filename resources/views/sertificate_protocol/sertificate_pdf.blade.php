@extends('layouts.pdf')
@section('styles')
@endsection
@section('content')
    @php
        use SimpleSoftwareIO\QrCode\Facades\QrCode;
    @endphp

    @include('sertificate_protocol._sertificate_cheque')

@endsection
