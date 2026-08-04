@extends('layouts.app')
@section('content')
    <!-- page content -->
    <?php $userid = Auth::user()->id; ?>
    @can('viewAny', \App\Models\User::class)
        <div class="section">
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans("app.Sinov bayonnomasi")}} -
                        {{ trans('app.Edit') }}
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
                                            <a href="{{ route('sertificate_protocol.list') }}">
                                                <span class="visible-xs"></span>
                                                <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Ro\'yxat')}}
                                            </a>
                                        </li>
                                        <li class="active">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-edit fa-lg">&nbsp;</i>
                                            <b>{{ trans('app.Edit')}}</b>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            @if($result->status == 1)
                                <div class="alert alert-warning">
                                    Bayonnoma fayli allaqachon yaratilgan. O'zgartirgandan so'ng faylni qaytadan
                                    yaratish kerak.
                                </div>
                            @endif
                            <form id="invoice-form" method="post"
                                  action="{{ route('sertificate_protocol.update', $dalolatnoma) }}"
                                  enctype="multipart/form-data"
                                  data-parsley-validate class="form-horizontal form-label-left">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('number') ? ' has-error' : '' }}">
                                        <label for="number"
                                               class="form-label certificate">{{trans('app.Sinov bayonnoma raqami')}}<label
                                                class="text-danger">*</label></label>

                                        <input type="number" class="form-control" maxlength="10"
                                               value="{{ old('number', $result->number) }}" name="number" required>
                                        @if ($errors->has('number'))
                                            <span class="help-block">
                                                 <strong>{{trans("app.Natija raqami noto'g'ri shaklda kiritilgan")}}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 form-group {{ $errors->has('date') ? ' has-error' : '' }}">
                                        <label class="form-label certificate">{{trans("app.Bayonnoma sanasi")}} <label
                                                class="text-danger">*</label></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="date_of_birth" class="form-control date"
                                                   placeholder="<?php echo getDatepicker();?>" name="date"
                                                   value="{{ old('date', $result->date ? \Carbon\Carbon::parse($result->date)->format('d-m-Y') : '') }}"
                                                   onkeypress="return false;" required/>
                                        </div>
                                        @if ($errors->has('date'))
                                            <span class="help-block">
                                                <strong style="color:red;">{{trans("app.Sana noto'g'ti shaklda kiritilgan")}}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="col-12 col-md-4"
                                         @if(getApplicationType() == \App\Models\CropsName::CROP_TYPE_4) style="display: none" @endif>
                                        <div class="form-group">
                                            <label class="form-label"
                                                   for="first-name">{{ trans('app.Operator nomi')}} <label
                                                    class="text-danger">*</label>
                                            </label>
                                            <select name="operator_id" class="form-control">
                                                @foreach($operators as $operator)
                                                    <option value="{{ $operator->id }}"
                                                            @if(old('operator_id', $result->operator_id) == $operator->id) selected @endif>
                                                        {{ $operator->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <a class="btn btn-primary"
                                       href="{{ route('sertificate_protocol.list') }}">{{ trans('app.Cancel')}}</a>
                                    <button type="submit" onclick="disableButton()" id="submitter"
                                            class="btn btn-success">{{ trans('app.Submit')}}</button>
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
                    <span class="titleup text-danger"><i class="fa fa-exclamation-circle"
                                                         aria-hidden="true"></i>&nbsp {{ trans('app.You Are Not Authorize This page.')}}</span>
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
            setTimeout(function () {
                button.disabled = false;
                button.innerText = 'Saqlash'; // Restore the button text
            }, 3000);
        }
    </script>
@endsection
