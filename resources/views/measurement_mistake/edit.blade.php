@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp O'lchash xatoligi bo'yicha bayonnomani o'zgartirish
                </li>
            </ol>
        </div>
        @can('create', \App\Models\Application::class)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="panel panel-primary">
                            <div class="tab_wrapper page-tab">
                                <ul class="tab_list">
                                    <li>
                                        <a href="{!! url('/measurement_mistake/search') !!}">
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
                                <div
                                    class="col-md-6 form-group has-feedback {{ $errors->has('number') ? ' has-error' : '' }}">
                                    <label for="number" class="form-label certificate">Bayonnoma raqami <label
                                            class="text-danger">*</label></label>
                                    <input type="number" class="form-control" value="{{ $result->number }}" name="number"
                                           required>
                                    @if ($errors->has('number'))
                                        <span class="help-block">
                                                    <strong class="text-danger">{{$errors->first('number')}}</strong>
                                                </span>
                                    @endif
                                </div>
                                <div class="col-md-6 form-group {{ $errors->has('date') ? ' has-error' : '' }}">
                                    <label class="form-label certificate">Bayonnoma sanasi <label
                                            class="text-danger">*</label></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                            </div>
                                        </div>
                                        <input type="text" id="date" class="form-control date"
                                               placeholder="<?php echo getDatepicker(); ?>" name="date" value="{{ date(getDateFormat(),strtotime($result->date)) }}"
                                               onkeypress="return false;" required />
                                    </div>
                                    @if ($errors->has('date'))
                                        <span class="help-block">
                                                    <strong class="text-danger">{{$errors->first('date')}}</strong>
                                                </span>
                                    @endif
                                </div>
                                <div class="form-group col-md-12 col-sm-12 pt-2">
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="{{ URL::asset('vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{ URL::asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ URL::asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $("input.date").datetimepicker({
            format: "dd-mm-yyyy",
            autoclose: 1,
            minView: 2,
            startView: 'decade',
            endDate: new Date(),
        });

        function disableButton() {
            var button = document.getElementById('submitter');
            button.disabled = true;
            button.innerText = 'Yuklanmoqda...'; // Optionally, change the text to indicate processing
            setTimeout(function() {
                button.disabled = false;
                button.innerText = 'Saqlash'; // Restore the button text
            }, 5000);
        }
    </script>
@endsection
