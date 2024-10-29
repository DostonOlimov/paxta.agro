@extends('layouts.app')
@section('content')
    <!-- page content -->
    <?php $userid = Auth::user()->id; ?>
    @can('view',\App\Models\Application::class)
        <div class="section">
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp Sertifikat ma'lumotlarini qo'shish
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
                                            <a href="{!! url('/final_results/search')!!}">
                                                <span class="visible-xs"></span>
                                                <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Ro\'yxat')}}
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
                            <form id="invoice-form" method="post" action="{!! url('final_results/store') !!}" enctype="multipart/form-data"
                                  data-parsley-validate class="form-horizontal form-label-left">
                                @csrf
                                <div class="row" >

                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <input type="hidden"  name="id" value="{{ $id}}" >
                                    <div class="certificate row">
                                        <div class="col-md-4">
                                            <label class="form-label certificate">Sertifikat faylini yuklang</label>
                                            <input class="form-control" type="file" placeholder="Asos hujjatni yuklang..."
                                                   required name="reason-file"
                                                   accept="application/pdf"
                                            />
                                        </div>
                                        <div class="col-md-4 form-group has-feedback {{ $errors->has('reestr_number') ? ' has-error' : '' }}">
                                            <label for="number" class="form-label ">Sertifikat reestr raqami <label class="text-danger">*</label></label>
                                            <input type="text" class="form-control" id="reestr_number"  value="{{ old('reestr_number')}}"  name="reestr_number" required>
                                            @if ($errors->has('reestr_number'))
                                                <span class="help-block">
											 <strong>
                                                 Sertifikat raqami noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                            @endif
                                        </div>
                                        <div class="col-md-4 form-group {{ $errors->has('given_date') ? ' has-error' : '' }}">
                                            <label class="form-label ">Sertifikat berilgan sana <label class="text-danger">*</label></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                    </div>
                                                </div>
                                                <input type="text" id="given_date" class="form-control given_date" placeholder="<?php echo getDatepicker();?>" name="given_date" value="{{ old('given_date') }}" onkeypress="return false;" required/>
                                            </div>
                                            @if ($errors->has('given_date'))
                                                <span class="help-block">
											<strong style="margin-left:27%;">Sana noto'g'ti shaklda kiritilgan</strong>
										</span>
                                            @endif
                                        </div>
                                    </div>

                                        <div class="col-md-6 col-sm-6">
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
    <script src="{{ asset('vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript">

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
    <script>
        $(document).ready(function(){

            $('input[name="fake-image"]').on('click', function(){
                $('input[name="image"]').click();
            });
            $('input[name="image"]').on('change',function(){
                $('textarea[name="file-name"]').text($('input[name="image"]').val());
            });

        });
    </script>
    <script>
        function disableButton() {
            var button = document.getElementById('submitter');
            button.disabled = true;
            button.innerText = 'Yuklanmoqda...'; // Optionally, change the text to indicate processing
            setTimeout(function() {
                button.disabled = false;
                button.innerText = 'Saqlash'; // Restore the button text
            }, 1000);
        }
    </script>
@endsection

