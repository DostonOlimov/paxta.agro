@extends('layouts.app')
@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp {{ trans('message.Foydalanuvchi ruxsatlari') }}
                </li>
            </ol>
        </div>
        <x-flash-message />

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ url('/permissions') }}" method="post">
                            @csrf
                            <div class="table-responsive">
                                <table id="datatable" class="table table-striped table-bordered" style="width:100%;">
                                    <thead>
                                    <tr>
                                        <th style="width:60px;">#</th>
                                        <th>{{ trans('app.Name') }}</th>
                                        @foreach($permissions as $permission)
                                            <th>{{ $permission->name }}</th>
                                        @endforeach
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->full_name }}</td>
                                            @foreach($permissions as $permission)
                                                <td class="text-center">
                                                    <input type="checkbox"
                                                           name="permissions[{{ $user->id }}][]"
                                                           value="{{ $permission->id }}"
                                                        {{ $user->permissions->contains('id', $permission->id) ? 'checked' : '' }}>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-success mt-3">{{ trans('app.Submit') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
