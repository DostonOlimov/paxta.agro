@extends('layouts.app')
@section('content')

    <?php $userid = Auth::user()->id; ?>
    @can('update', \App\Models\User::class)
            <div class="section">
                <div class="page-header">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a ><i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans('app.Edit')}}
                            </a>
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
                                                <a href="{!! url('/klassiyor/list' )!!}">
                                                    <span class="visible-xs"></span>
                                                    <i class="fa fa-list fa-lg">&nbsp;</i>
                                                    {{ trans('app.Ro\'yxat')}}
                                                </a>
                                            </li>
                                            <li class="active">
                                                <a href="{!! url('/klassiyor/list/edit/'.$klassiyor->id )!!}">
                                                    <span class="visible-xs"></span>
                                                    <i class="fa fa-plus-circle fa-lg">&nbsp;</i> <b>
                                                        {{ trans('app.Tahrirlash')}}</b>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <form action="update/{{ $klassiyor->id }}" method="post"
                                              enctype="multipart/form-data" data-parsley-validate
                                              class="form-horizontal form-label-left">
                                            <div class="row">
                                                <div class="col-12 col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="first-name">{{trans('app.Klassiyor nomi')}} <label
                                                                class="text-danger">*</label>
                                                        </label>
                                                        <input type="text" required="required" value="{{$klassiyor->name}}" name="name"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="first-name">{{trans('app.Klassiyor kodi')}} <label
                                                                class="text-danger">*</label>
                                                        </label>
                                                        <input type="text" required="required" name="kode" value="{{$klassiyor->kode}}"
                                                               class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="first-name">{{ trans('app.Laboratoriya nomi')}} <label
                                                                class="text-danger">*</label>
                                                        </label>
                                                        <select name="laboratory_id" class="region form-control" required>
                                                            @if(!empty($laboratories))
                                                                @foreach($laboratories as $region)
                                                                    <option
                                                                        value="{{ $region->id }}" @if($region->id == $klassiyor->laboratory_id) selected @endif>{{ $region->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label class="form-label" style="visibility: hidden;">label</label>
                                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                                    <a class="btn btn-primary"
                                                       href="{{ URL::previous() }}">{{ trans('app.Cancel')}}</a>
                                                    <button type="submit"
                                                            class="btn btn-success">{{ trans('app.Update')}}</button>
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
