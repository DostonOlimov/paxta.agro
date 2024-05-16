@extends('layouts.app')
@section('content')
    <!-- page content -->
    @can('create', \App\Models\Application::class)
        <div class="section">
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp Tara og'irligini o'zgartirish
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
                                            <a href="{!! url('/akt_amount/search') !!}">
                                                <span class="visible-xs"></span>
                                                <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Ro\'yxat') }}
                                            </a>
                                        </li>
                                        <li class="active">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-pencil fa-lg">&nbsp;</i>
                                            <b>{{ trans('O\'zgartirish') }}</b>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <form id="invoice-form" method="post" action="update/{{ $result->id }}"
                                  enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">
                                @csrf
                                <div class="row">
                                        <div class="col-md-6 form-group has-feedback {{ $errors->has('nav') ? ' has-error' : '' }}">
                                            <label for="number" class="form-label ">Tara (kg)</label>
                                            <input type="number" class="form-control" step="0.001" value="{{ $result->tara }}"  name="tara" required>
                                            @if ($errors->has('tara'))
                                                <span class="help-block">
                                                    <strong>
                                                        Tara no'g'ri shaklda kiritilgan</strong>
                                                </span>
                                            @endif
                                        </div>
                                    <div class="form-group col-md-12 col-sm-12">
                                        <div class="col-md-6 col-sm-6">
                                            <a class="btn btn-primary"
                                               href="{{ URL::previous() }}">{{ trans('app.Cancel') }}</a>
                                            <button type="submit" onclick="disableButton()" id="submitter"
                                                    class="btn btn-success">{{ trans('app.Submit') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
@endsection
