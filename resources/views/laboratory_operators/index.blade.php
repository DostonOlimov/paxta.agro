@extends('layouts.app')

@section('content')
    @can('viewAny', \App\Models\User::class)
        <div class="section">
            <!-- Page Header -->
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp {{ trans('app.Laboratory Operators') }}
                    </li>
                </ol>
            </div>

            <!-- Session-based messages -->
            @if (session('message'))
                <div class="row">
                    <div class="col-md-12">
                        <div
                            class="alert text-center
                            @php
                                echo (session('message') == 'Cannot Delete' || session('message') == 'Duplicate Data') ? 'alert-danger' : 'alert-success'; @endphp">
                            @switch(session('message'))
                                @case('Successfully Submitted')
                                    {{ trans('app.Successfully Submitted') }}
                                @break

                                @case('Successfully Updated')
                                    {{ trans('app.Successfully Updated') }}
                                @break

                                @case('Successfully Deleted')
                                    {{ trans('app.Successfully Deleted') }}
                                @break

                                @case('Duplicate Data')
                                    {{ trans('app.Duplicate Data') }}
                                @break

                                @case('Cannot Delete')
                                    {{ trans('app.Cannot Delete') }}
                                @break
                            @endswitch
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main content -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="panel panel-primary">
                                <div class="tab_wrapper page-tab">
                                    <ul class="tab_list">
                                        <li class="active">
                                            <a href="{{ route('laboratory_operators.index') }}">
                                                <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.List') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('laboratory_operators.create') }}">
                                                <i class="fa fa-plus-circle fa-lg">&nbsp;</i> <b>{{ trans('app.Add') }}</b>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered nowrap" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ trans('app.Operator Name') }}</th>
                                            <th>{{ trans('app.Laboratory Name') }}</th>
                                            <th>{{ trans('app.Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($operators as $index => $operator)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $operator->name }}</td>
                                                <td>{{ optional($operator->laboratory)->name }}</td>
                                                <td>
                                                    <a href="{{ route('laboratory_operators.edit', $operator->id) }}">
                                                        <button type="button"
                                                            class="btn btn-success">{{ trans('app.Edit') }}</button>
                                                    </a>

                                                    <form action="{{ route('laboratory_operators.destroy', $operator->id) }}" method="POST">
                                                        @csrf
                                                        @method("DELETE")
                                                        <button type="submit"
                                                        class="btn btn-round btn-danger dgr">{{ trans('app.Delete') }}</button>
                                                    </form>

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="section" role="main">
            <div class="card">
                <div class="card-body text-center">
                    <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp
                        {{ trans('app.You Are Not Authorize This page.') }}</span>
                </div>
            </div>
        </div>
    @endcan

    <script src="{{ asset('vendors/jquery/dist/jquery.min.js') }}"></script>
    {{-- <script>
        $('body').on('click', '.sa-warning', function() {

            var url = $(this).attr('url');

            swal({
                title: "O'chirishni istaysizmi?",
                text: "O'chirilgan ma'lumotlar qayta tiklanmaydi!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#297FCA",
                confirmButtonText: "Ha, o'chirish!",
                cancelButtonText: "O'chirishni bekor qilish",
                closeOnConfirm: false
            }).then((result) => {
                window.location.href = url;

            });
        });
    </script> --}}
@endsection
