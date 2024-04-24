@extends('layouts.app')
@section('styles')
    <style>
        .form-group {
            margin-bottom: 0 !important;
        }
    </style>
@endsection
@section('content')
    <!-- page content -->
    @can('create', \App\Models\Application::class)
        <div class="section">
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp Na'mlik natijalarini o'zgartirish
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
                                            <a href="{!! url('/humidity_result/search') !!}">
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
                                        <label for="number" class="form-label certificate">Dalolatnoma raqami <label
                                                class="text-danger">*</label></label>
                                        <input type="number" class="form-control" value="{{ $result->number }}" name="number"
                                            required>
                                        @if ($errors->has('number'))
                                            <span class="help-block">
                                                <strong>Dalolatnoma raqami noto'g'ri shaklda kiritilgan</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 form-group {{ $errors->has('date') ? ' has-error' : '' }}">
                                        <label class="form-label certificate">Dalolatnoma sanasi <label
                                                class="text-danger">*</label></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="date" class="form-control date"
                                                placeholder="<?php echo getDatepicker(); ?>" name="date" value="{{ $result->date }}"
                                                onkeypress="return false;" required />
                                        </div>
                                        @if ($errors->has('date'))
                                            <span class="help-block">
                                                <strong style="margin-left:27%;">Sana noto'g'ti shaklda kiritilgan</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="row pt-5">
                                        <div class="col-md-6 form-group">
                                            <h5 style="color: blue">Tolaning quritishgacha vazni,m₀( kalibrovka sertifikatidagi farq xisobi bilan),g</h5>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <h5 style="color: blue">Tolaning quritilgandan keyingi vazni,mk( kalibrovka sertifikatidagi farq xisobi bilan),g</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 form-group has-feedback {{ $errors->has('m0') ? ' has-error' : '' }}">
                                            <label for="number" class="form-label ">Na'muna1<label class="text-danger">*</label> </label>
                                            <input type="number" step="0.01"  class="form-control" value="{{ $result->m0 }}"  name="m0" required>
                                            @if ($errors->has('m0'))
                                                <span class="help-block">
                                                    <strong class="text-danger">{{$errors->first('m0')}}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6 form-group has-feedback {{ $errors->has('mk0') ? ' has-error' : '' }}">
                                            <label for="number" class="form-label ">Na'muna1<label class="text-danger">*</label></label>
                                            <input type="number" step="0.01"  class="form-control"  value="{{ $result->mk0 }}"  name="mk0" required>
                                            @if ($errors->has('mk0'))
                                                <span class="help-block">
                                                        <strong class="text-danger">{{$errors->first('mk0')}}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 form-group has-feedback {{ $errors->has('m1') ? ' has-error' : '' }}">
                                            <label for="number" class="form-label ">Na'muna2<label class="text-danger">*</label></label>
                                            <input type="number" step="0.01"  class="form-control" value="{{ $result->m1 }}"  name="m1" required>
                                            @if ($errors->has('m1'))
                                                <span class="help-block">
                                                    <strong class="text-danger">{{$errors->first('m1')}}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6 form-group has-feedback {{ $errors->has('mk1') ? ' has-error' : '' }}">
                                            <label for="number" class="form-label ">Na'muna2<label class="text-danger">*</label></label>
                                            <input type="number" step="0.01" class="form-control" value="{{ $result->mk1 }}"  name="mk1" required>
                                            @if ($errors->has('mk1'))
                                                <span class="help-block">
                                                    <strong class="text-danger">{{$errors->first('mk1')}}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group has-feedback {{ $errors->has('kalibrovka') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label ">Kalibrovka sertifikatida keltirilgan kengaytirilgan noaniqlik, Ur<label class="text-danger">*</label></label>
                                        <input type="number" step="0.0001" class="form-control" value="{{ $result->kalibrovka }}"  name="kalibrovka" required>
                                        @if ($errors->has('kalibrovka'))
                                            <span class="help-block">
                                                    <strong class="text-danger">{{$errors->first('kalibrovka')}}</strong>
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
