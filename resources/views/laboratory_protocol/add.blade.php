@extends('layouts.app')
@section('content')
    <!-- page content -->
    <?php $userid = Auth::user()->id; ?>
    @can('viewAny', \App\Models\User::class)
        <div class="section">
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans("app.Sinov bayonnomasi qo'shish")}}
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
                                            <a href="{!! url()->previous()!!}">
                                                <span class="visible-xs"></span>
                                                <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Orqaga')}}
                                            </a>
                                        </li>
                                        <li class="active">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-plus-circle fa-lg">&nbsp;</i>
                                            <b>{{ trans('app.Qo\'shish')}}</b>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <form id="invoice-form" method="post" action="{!! url('laboratory-protocol/store') !!}" enctype="multipart/form-data"
                                  data-parsley-validate class="form-horizontal form-label-left">
                                @csrf
                                <div class="row" >
                                    @csrf
                                    <input type="hidden"  name="dalolatnoma_id" value="{{ $apps->id}}" >

                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('number') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label certificate">{{trans('app.Sinov bayonnoma raqami')}}<label class="text-danger">*</label></label>
                                        {{-- @if($test->laboratory_results->quality == 1)
                                            <label for="number" class="form-label certificate">Sinov bayonnoma raqami <label class="text-danger">*</label></label>
                                        @else
                                            <label for="number" class="form-label nocertificate">Taxlil natija raqami <label class="text-danger">*</label></label>
                                        @endif --}}
                                        <input type="number" class="form-control" maxlength="10" value="{{ old('number')}}"  name="number" required>
                                        @if ($errors->has('number'))
                                            <span class="help-block">
											 <strong>{{trans("app.Natija raqami noto'g'ri shaklda kiritilgan")}}</strong>
										   </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 form-group {{ $errors->has('date') ? ' has-error' : '' }}">
                                        <label class="form-label certificate">{{trans("app.Bayonnoma sanasi")}} <label class="text-danger">*</label></label>
                                        {{-- @if($test->laboratory_results->quality == 1)
                                            <label class="form-label certificate">Bayonnoma sanasi <label class="text-danger">*</label></label>
                                        @else
                                            <label class="form-label nocertificate"> Tahlil natija sanasi<label class="text-danger">*</label></label>
                                        @endif --}}
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="date_of_birth" class="form-control date" placeholder="<?php echo getDatepicker();?>" name="date" value="{{ old('date') }}" onkeypress="return false;" required />
                                        </div>
                                        @if ($errors->has('start_date'))
                                            <span class="help-block">
											<strong style="color:red;">{{trans("app.Sana noto'g'ti shaklda kiritilgan")}}</strong>
										</span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('from') ? ' has-error' : '' }}">
                                        <label for="from" class="form-label certificate">{{trans("app.Namuna tanlab olish joyi")}}<label class="text-danger">*</label></label>
                                        <select name="from" class="form-control" required>
                                            <option value="">{{trans("app.Namuna tanlab olish joyini tanlang")}}</option>
                                            <option value="presslash sexidan">{{trans("app.Presslash sexidan")}}</option>
                                            <option value="zavodomboridan">{{trans("app.Zavod omboridan")}}</option>
                                        </select>

                                    </div>
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('vakili') ? ' has-error' : '' }}">
                                        <label for="vakili" class="form-label certificate">{{trans("app.Namuna tanlab olish vakili")}}<label class="text-danger">*</label></label>
                                        <select name="vakili" class="form-control" required>
                                            <option value="">{{trans("app.Namuna tanlab olish vakili tanlang")}}</option>
                                            <option value="sertifikatlash idorasi vakili">{{trans("app.sertifikatlash idorasi vakili")}}</option>
                                            <option value="guruh rahbari">{{trans("app.guruh rahbari")}}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('vakil_name') ? ' has-error' : '' }}">
                                        <label for="vakil_name" class="form-label certificate">{{trans("app.Namuna tanlab olish vakil nomi")}}<label class="text-danger">*</label></label>
                                        <input type="text" step="any" class="form-control" value="{{ old('vakil_name')}}"  name="vakil_name" required>
                                        @if ($errors->has('vakil_name'))
                                            <span class="help-block">
											 <strong>{{trans("app.Natija noto'g'ri shaklda kiritilgan")}}</strong>
										   </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('harorat') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label ">{{trans("app.Xona harorati")}} °C<label class="text-danger">*</label></label>
                                        <input type="number" step="any" class="form-control" id="harorat" value="{{ old('harorat')}}"  name="harorat" required >
                                        @if ($errors->has('harorat'))
                                            <span class="help-block">
											 <strong>
                                                {{trans("app.Harorat no'to'gri shaklda kiritilgan")}} </strong>
										   </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('namlik') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label ">{{trans("app.Nisbiy namlik")}} % <label class="text-danger">*</label></label>
                                        <input type="number" step="any" class="form-control" id="namlik" value="{{ old('namlik')}}"  name="namlik" required>
                                        @if ($errors->has('namlik'))
                                            <span class="help-block">
											 <strong>
                                                {{trans("app.Namlik no'to'gri shaklda kiritilgan")}}</strong>
										   </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('yoruglik') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label ">{{trans("app.Yorug'lik sharoiti")}} % <label class="text-danger">*</label></label>
                                        <input type="number" step="any" class="form-control" id="yoruglik" value="{{ old('yoruglik')}}"  name="yoruglik" required>
                                        @if ($errors->has('yoruglik'))
                                            <span class="help-block">
											 <strong>
                                                 {{trans("app.Yorug'lik no'to'gri shaklda kiritilgan")}}</strong>
										   </span>
                                        @endif
                                    </div>
                                    {{-- <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"
                                                   for="first-name">{{ trans('app.Klassiyor nomi')}} <label
                                                    class="text-danger">*</label>
                                            </label>
                                            <select name="klassiyor_id" class="form-control">
                                                <option value="">{{ trans('app.Klassiyorni tanlang')}}</option>
                                                    @foreach($apps->decision->laboratory->klassiyor as $klassiyor)
                                                        <option
                                                            value="{{ $klassiyor->id }}">{{ $klassiyor->name }}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"
                                                   for="first-name">{{ trans('app.Operator nomi')}} <label
                                                    class="text-danger">*</label>
                                            </label>
                                            <select name="operator_id" class="form-control">
                                                    <option value="">{{ trans('app.Operatorni tanlang')}}</option>
                                                    @foreach($operators as $operator)
                                                        <option
                                                            value="{{ $operator->id }}">{{ $operator->name }}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="director_id" value="{{$director->id}}">
                                    <input type="hidden" name="klassiyor_id" value="{{optional($klassiyor->klassiyor)->id}}">
                                    {{-- <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"
                                                   for="first-name">{{ trans('app.Laboratoriya director nomi')}} <label
                                                    class="text-danger">*</label>
                                            </label>
                                            <select name="director_id" class="form-control">
                                                    <option value="">{{ trans('app.Laboratoriya directorni tanlang')}}</option>
                                                    @foreach($director as $item)
                                                        <option
                                                            value="{{ $item->id }}">{{ $item->lastname.' '.$item->name }}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                </div>
                                    <div class="col-md-6 col-sm-6">
                                        <a class="btn btn-primary" href="{{ URL::previous() }}">{{ trans('app.Cancel')}}</a>
                                        <button type="submit" onclick="disableButton()" id="submitter" class="btn btn-success">{{ trans('app.Submit')}}</button>
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
                    <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp {{ trans('app.You Are Not Authorize This page.')}}</span>
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
                startView:'decade',
                endDate: new Date(),
            });

        function disableButton() {
            var button = document.getElementById('submitter');
            button.disabled = true;
            button.innerText = 'Yuklanmoqda...'; // Optionally, change the text to indicate processing
            setTimeout(function() {
                button.disabled = false;
                button.innerText = 'Saqlash'; // Restore the button text
            }, 3000);
        }
    </script>
@endsection

