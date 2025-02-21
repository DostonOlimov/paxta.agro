@extends('layouts.app')

@section('title', '500 - Internal Server Error')

@section('content')
    <div class="container text-center pt-5">
        <h1 class="text-danger">500 - Internal Server Error</h1>
        <p class="text-muted">
            Kutilmagan xatolik yuz berdi. Xatolik xabari berildi va uni tuzatish ishlari amalga oshirilmoqda.
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

