@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Tahrirlash</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{ route('users.list') }}" enctype="multipart/form-data">
                        Orqaga</a>
                </div>
            </div>
        </div>
        @if(session('status'))
            <div class="alert alert-success mb-1 mt-1">
                {{ session('status') }}
            </div>
        @endif
        <form action="{{ route('users.update',$user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>User Name:</strong>
                        <input type="text" name="username" class="form-control" value="{{ $user ->username }}" >
                        @error('username')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <strong>Parol:</strong>
                        <input type="text" name="password" class="form-control" >
                        @error('password')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="form-group">
                        <strong>Ismi:</strong>
                        <input type="text" name="first_name" class="form-control"value="{{ strchr($user->name ,' ',true)}}" >
                        @error('first_name')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="form-group">
                        <strong>Familiyasi:</strong>
                        <input type="text" name="last_name" class="form-control" value="{{ strchr($user->name,' ') }}">
                        @error('last_name')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="form-group">
                        <strong>Roli:</strong>
                        <select name="role_id" class="form-control" id="" value="{{ $user->role_id }}" >
                            @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                        <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary ml-3">Saqlash</button>
        </form>
    </div>
@endsection
