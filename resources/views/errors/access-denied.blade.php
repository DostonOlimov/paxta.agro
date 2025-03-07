@extends('layouts.app')

@section('title', 'Foydalanishga ruxsat berilmagan')

@section('content')

    <div class="container text-center pt-5">
        <h1 class="text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"> </i> &nbsp;Kirish taqiqlandi!</h1>
        <p class="text-muted">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif
        </p>
        <div class="mt-4">
            <a href="{{ url()->previous() }}" class="btn btn-outline-danger">
                <i class="fa fa-arrow-left"></i> {{ trans('app.Orqaga') }}
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                <i class="fa fa-home"></i> {{ trans('message.Bosh sahifa') }}
            </a>
        </div>
    </div>
@endsection
