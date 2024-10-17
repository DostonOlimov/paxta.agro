@extends('layouts.app')
@section('content')
    <!-- page content -->
    <?php $userid = Auth::user()->id; ?>
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="bi bi-people-fill mr-1"></i>&nbsp {{ trans('app.Employee')}}
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
                                        <a href="{!! url('/employee/list')!!}">
                                            <span class="visible-xs"></span>
                                            <i class="bi bi-list-ul">&nbsp;</i> {{ trans('app.Ro\'yxat')}}
                                        </a>
                                    </li>
                                    <li class="active">
                                        <span class="visible-xs"></span>
                                        <i class="bi bi-plus-circle">&nbsp;</i>
                                        <b>{{ trans('app.Qo\'shish')}}</b>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <form method="post" action="{!! url('employee/add_store') !!}" enctype="multipart/form-data"  class="form-horizontal upperform">
                            <div class="row">
                                <input type="hidden" name="_token" value="{{csrf_token()}}">


                                <div class="col-md-4 form-group has-feedback">
                                    <label class="form-label" for="file">Fayl <label class="text-danger">*</label></label>
                                    <div class="form-control">
                                        <input name="file" type="file" class="" >
                                    </div>
                                </div>

                                <div class="form-group col-md-12 col-sm-12">
                                    <div class="col-md-12 col-sm-12 text-center">
                                        <a class="btn btn-primary" href="{{ URL::previous() }}">{{ trans('app.Cancel')}}</a>
                                        <button type="submit" class="btn btn-success">{{ trans('app.Submit')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="{{ URL::asset('vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{ URL::asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ URL::asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>


@endsection
