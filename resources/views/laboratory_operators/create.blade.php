@extends('layouts.app')

@section('content')
    @can('create', \App\Models\User::class)
        <div class="section">
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp {{ trans("app.Add Laboratory Operator") }}
                    </li>
                </ol>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="panel panel-primary">
                                <div class="tab_wrapper page-tab">
                                    <ul class="tab_list">
                                        <li>
                                            <a href="{{ route('laboratory_operators.index') }}">
                                                <i class="fa fa-list fa-lg">&nbsp;</i>
                                                {{ trans('app.Ro\'yxat') }}
                                            </a>
                                        </li>
                                        <li class="active">
                                            <a href="{{ route('laboratory_operators.create') }}">
                                                <i class="fa fa-plus-circle fa-lg">&nbsp;</i> <b>{{ trans('app.Qo\'shish') }}</b>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <form action="{{ route('laboratory_operators.store') }}" method="post" enctype="multipart/form-data" data-parsley-validate>
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>{{ trans("app.Name") }} <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>{{ trans("app.Laboratory Name") }} <span class="text-danger">*</span></label>
                                                    <select name="laboratory_id" class="region form-control" required>
                                                        @foreach ($laboratories as $laboratory)
                                                            <option value="{{ $laboratory->id }}">{{ $laboratory->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label style="visibility: hidden;"></label>
                                                <div class="form-group">
                                                    <a class="btn btn-primary" href="{{ URL::previous() }}">{{ trans("app.Cancel") }}</a>
                                                    <button type="submit" class="btn btn-success">{{ trans("app.Submit") }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="section">
            <div class="card">
                <div class="card-body text-center">
                    <span class="titleup text-danger">
                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp;
                        {{ trans("app.You Are Not Authorized to Access This Page") }}
                    </span>
                </div>
            </div>
        </div>
    @endcan

    <script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>
@endsection
