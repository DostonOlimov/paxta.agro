@extends('layouts.app')

@section('title', 'Foydalanishga ruxsat berilmagan')

@section('content')
    <div class="container text-center">
        <h1 class="text-danger">Access Denied</h1>
        <p>You do not have permission to access this page.</p>
        <a href="{{ route('home') }}" class="btn btn-primary">Go Back</a>
    </div>
@endsection
