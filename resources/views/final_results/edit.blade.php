@extends('layouts.app')
@section('content')
    <!-- page content -->
    @can('create', \App\Models\Application::class)
        <div class="section">
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp Sinov dasturini o'zgartirish
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
                                            <a href="{!! url('/tests/search')!!}">
                                                <span class="visible-xs"></span>
                                                <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Ro\'yxat')}}
                                            </a>
                                        </li>
                                        <li class="active">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-pencil fa-lg">&nbsp;</i>
                                            <b>{{ trans('O\'zgartirish')}}</b>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <form id="invoice-form" method="post" action="update/{{ $test->id }}" enctype="multipart/form-data"
                                  data-parsley-validate class="form-horizontal form-label-left">
                                @csrf
                                <div class="row" >

                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <input type="hidden"  name="test_id" value="{{ $test->id}}" >
                                    <div class="col-md-4 form-group has-feedback">
                                        <label class="form-label" for="app_number">Ariza raqami <label class="text-danger">*</label></label>
                                        <input type="number" readonly name="app_number" value="{{ $test->application->app_number}}" class="form-control">
                                    </div>
                                    <div class="col-md-4 form-group has-feedback">
                                        <label class="form-label" for="app_number">Mahsulot nomi <label class="text-danger">*</label></label>
                                        <input type="text" readonly name="product_name" value="{{ optional($test->application->crops)->name->name}}" class="form-control">
                                    </div>
                                    <div class="col-md-4 form-group has-feedback">
                                        <label class="form-label" for="app_number">Mahsulot navi <label class="text-danger">*</label></label>
                                        <input type="text" readonly name="product_type" value="{{ optional($test->application->crops)->type->name}}" class="form-control">
                                    </div>
                                    <div class="col-md-4 form-group has-feedback">
                                        <label class="form-label" for="app_number">Mahsulot avlodi <label class="text-danger">*</label></label>
                                        <input type="text" readonly name="product_generation" value="{{ optional($test->application->crops)->generation->name}}" class="form-control">
                                    </div>
                                    <div class="col-md-4 form-group has-feedback">
                                        <label class="form-label" for="app_number">Sinov dasturi sanasi <label class="text-danger">*</label></label>
                                        <input type="text" readonly name="app_number" value="{{ $test->application->date}}" class="form-control">
                                    </div>
                                    <div class="col-md-4 form-group has-feedback">
                                        <label class="form-label" for="app_number">Setifikat berilganligi <label class="text-danger">*</label></label>
                                        <input type="text" readonly name="app_number" value="@if($certificate) Berilgan @else Berilmagan @endif" class="form-control">
                                    </div>

                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('number') ? ' has-error' : '' }}">
                                        @if($certificate)
                                        <label for="number" class="form-label certificate">Sinov bayonnoma raqami <label class="text-danger">*</label></label>
                                        @else
                                        <label for="number" class="form-label nocertificate">Taxlil natija raqami <label class="text-danger">*</label></label>
                                       @endif
                                        <input type="number" class="form-control" maxlength="10" value="{{ $result->number}}"  name="number" required>
                                        @if ($errors->has('number'))
                                            <span class="help-block">
											 <strong>Natija raqami noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 form-group {{ $errors->has('date') ? ' has-error' : '' }}">
                                        @if($certificate)
                                        <label class="form-label certificate">Bayonnoma sanasi <label class="text-danger">*</label></label>
                                        @else
                                        <label class="form-label nocertificate"> Tahlil natija sanasi<label class="text-danger">*</label></label>
                                        @endif
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="date" class="form-control date" placeholder="<?php echo getDatepicker();?>" name="date" value="{{ $result->date }}" onkeypress="return false;" required />
                                        </div>
                                        @if ($errors->has('date'))
                                            <span class="help-block">
											<strong style="margin-left:27%;">Sana noto'g'ti shaklda kiritilgan</strong>
										</span>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        @if($certificate)
                                        <label class="form-label certificate">Bayonnoma faylini yuklang</label>
                                        @else
                                        <label class="form-label nocertificate">Tahlil faylini yuklang</label>
                                        @endif
                                        <input class="form-control" type="file" placeholder="Asos hujjatni yuklang..."
                                               required name="reason-file"
                                               accept="application/pdf"
                                        />
                                            <div class="col-md-7 col-sm-12 table_td">
                                            <span class="txt_color">
                                                 @if(optional($result)->attachment)
                                                    <a href="{{route('attachment.download', ['id' => $result->attachment->id])}}" class="text-azure">
                                                                    <i class="fa fa-download"></i> Asos fayli
                                                                </a>
                                                @endif
                                            </span>
                                            </div>
                                    </div>
                                    @if($certificate)
                                    <div class="certificate row">
                                        <div class="col-md-6 form-group has-feedback {{ $errors->has('reestr_number') ? ' has-error' : '' }}">
                                            <label for="number" class="form-label ">Sertifikat reestr raqami <label class="text-danger">*</label></label>
                                            <input type="number" class="form-control" id="reestr_number" maxlength="10" value="{{ $certificate->reestr_number}}"  name="reestr_number" required>
                                            @if ($errors->has('reestr_number'))
                                                <span class="help-block">
											 <strong>
                                                 Sertifikat raqami noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                            @endif
                                        </div>
                                        <div class="col-md-6 form-group {{ $errors->has('given_date') ? ' has-error' : '' }}">
                                            <label class="form-label ">Sertifikat berilgan sana <label class="text-danger">*</label></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                    </div>
                                                </div>
                                                <input type="text" id="given_date" class="form-control given_date" placeholder="<?php echo getDatepicker();?>" name="given_date" value="{{ $certificate->given_date }}" onkeypress="return false;" required/>
                                            </div>
                                            @if ($errors->has('given_date'))
                                                <span class="help-block">
											<strong style="margin-left:27%;">Sana noto'g'ti shaklda kiritilgan</strong>
										</span>
                                            @endif
                                        </div>
                                    </div>
                                    @else
                                    <div class="nocertificate row">
                                        <div class="col-md-4 form-group has-feedback">
                                            <label class="form-label">Mahsulot sifati <label class="text-danger">*</label></label>
                                            <div class=" gender">
                                                <label class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input"  name="type" value="0" @if($result->type == 0)checked @endif>
                                                    <span class="custom-control-label">Nomuvofiq </span>
                                                </label>
                                                <label class="custom-control custom-radio">
                                                    <input type="radio"  class="custom-control-input" name="type" value="1" @if($result->type == 1)checked @endif >
                                                    <span class="custom-control-label">Muvofiq </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 form-group has-feedback {{ $errors->has('folder_number') ? ' has-error' : '' }}">
                                            <label for="number" class="form-label ">Tikilgan papka raqami <label class="text-danger">*</label></label>
                                            <input type="text" class="form-control"  maxlength="25" value="{{ $result->folder_number}}"  name="folder_number" >
                                            @if ($errors->has('folder_number'))
                                                <span class="help-block">
											 <strong>Papka raqami noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                            @endif
                                        </div>
                                        <div class="col-md-4 form-group has-feedback">
                                            <label class="form-label" for="data">Izoh:<label class="text-danger">*</label></label>
                                            <div class="">
                                                <textarea id="comment" name="comment" class="form-control" maxlength="200" >{{ $result->comment}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="form-group col-md-12 col-sm-12">
                                        <div class="col-md-12 col-sm-12 text-center">
                                            <a class="btn btn-primary" href="{{ URL::previous() }}">{{ trans('app.Cancel')}}</a>
                                            <button type="submit" onclick="disableButton()" id="submitter" class="btn btn-success">{{ trans('app.Submit')}}</button>
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
        function disableButton() {
            var button = document.getElementById('submitter');
            button.disabled = true;
            button.innerText = 'Yuklanmoqda...'; // Optionally, change the text to indicate processing
        }
        $("input.date").datetimepicker({
            format: "dd-mm-yyyy",
            autoclose: 1,
            minView: 2,
            startView:'decade',
            endDate: new Date(),
        });
        $("input.given_date").datetimepicker({
            format: "dd-mm-yyyy",
            autoclose: 1,
            minView: 2,
            startView:'decade',
            endDate: new Date(),
        });
    </script>
@endsection


