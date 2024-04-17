@extends('layouts.app')
@section('content')
    @can('update', $app)
        <div class="section">
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fa fa-times mr-1"></i>&nbsp Arizani rad etish oynasi
                    </li>
                </ol>
            </div>
            <div class="clearfix"></div>
            @if(session('message'))
                <div class="row massage">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="alert alert-success danger text-center">

                            <label for="checkbox-10 colo_success"> {{ trans('app.Duplicate Data')}} </label>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="panel panel-primary">
                                <div class="tab_wrapper page-tab">
                                    <ul class="tab_list">
                                        <li>
                                            <a href="{!! url('/application/list')!!}">
                                                <span class="visible-xs"></span>
                                                <i class="fa fa-list fa-lg">&nbsp;</i>
                                                {{ trans('app.Ro\'yxat')}}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <form action="{{ url('/application/reject/store') }}" method="post"
                                          enctype="multipart/form-data" data-parsley-validate
                                          class="form-horizontal form-label-left">
                                        <div class="row">
                                            <div class="col-12 col-md-8">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                           for="first-name">Arizani rad etish sababini kiriting <label
                                                            class="text-danger">*</label>
                                                    </label>
                                                    <textarea  class="form-control" name="reason">

                                                    </textarea>
                                                </div>
                                            </div>

                                            <input type="hidden" name="_token" value="{{csrf_token()}}">
                                            <input type="hidden" name="app_id" value="{{$app->id}}">
                                            <div class="col-12 col-md-6">
                                                <label class="form-label" style="visibility: hidden;">label</label>
                                                <div class="form-group">
                                                    <a class="btn btn-primary"
                                                       href="{{ URL::previous() }}">{{ trans('app.Cancel')}}</a>
                                                    <button type="submit"
                                                            class="btn btn-success">{{ trans('app.Submit')}}</button>
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
        <div class="section" role="main">
            <div class="card">
                <div class="card-body text-center">
                    <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp {{ trans('app.You Are Not Authorize This page.')}}</span>
                </div>
            </div>
        </div>

    @endcan
    <script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.region').select2({
                minimumResultsForSearch: Infinity
            });
        })
    </script>
@endsection
