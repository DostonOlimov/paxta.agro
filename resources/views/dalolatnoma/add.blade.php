@extends('layouts.app')
@section('content')
<!-- page content -->
<?php $userid = Auth::user()->id; ?>
@can('view',\App\Models\Application::class)
   <div class="section">
		<div class="page-header">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<i class="fe fe-life-buoy mr-1"></i>&nbsp Na'muna olish dalolatnomasini qo'shish
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
										<a href="{!! url('/dalolatnoma/search')!!}">
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
						<form id="invoice-form" method="post" action="{!! url('dalolatnoma/store') !!}" enctype="multipart/form-data"
                              data-parsley-validate class="form-horizontal form-label-left">
                            @csrf
							<div class="row" >
								<input type="hidden" name="_token" value="{{csrf_token()}}">
                                <input type="hidden"  name="test_id" value="{{ $test->id}}" >

                                <div class="col-md-6 form-group has-feedback {{ $errors->has('number') ? ' has-error' : '' }}">
                                    <label for="number" class="form-label certificate">Dalolatnoma raqami <label class="text-danger">*</label></label>
                                    <input type="number" class="form-control" value="{{ old('number')}}"  name="number" required>
                                    @if ($errors->has('number'))
                                        <span class="help-block">
											 <strong>Dalolatnoma raqami noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                    @endif
                                </div>
                                <div class="col-md-6 form-group {{ $errors->has('date') ? ' has-error' : '' }}">
                                    <label class="form-label certificate">Dalolatnoma sanasi <label class="text-danger">*</label></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                            </div>
                                        </div>
                                        <input type="text" id="date" class="form-control date" placeholder="<?php echo getDatepicker();?>" name="date" value="{{ old('date') }}" onkeypress="return false;" required />
                                    </div>
                                    @if ($errors->has('date'))
                                        <span class="help-block">
											<strong style="margin-left:27%;">Sana noto'g'ti shaklda kiritilgan</strong>
										</span>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('selection_code') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label ">Seleksion navining kodi<label class="text-danger">*</label> </label>
                                        <input type="text" class="form-control" maxlength="10" value="{{ old('selection_code')}}"  name="selection_code" required>
                                        @if ($errors->has('selection_code'))
                                            <span class="help-block">
											 <strong>
                                                 Seleksiya kodi noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('toy_count') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label ">Jami na'munalar soni<label class="text-danger">*</label> </label>
                                        <input type="number" class="form-control" value="{{ old('toy_count')}}"  name="toy_count" required>
                                        @if ($errors->has('toy_count'))
                                            <span class="help-block">
											            <strong class="text-danger">{{$errors->first('toy_count')}}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('amount') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label ">Olingan na'munaning og'irligi(kg)<label class="text-danger">*</label></label>
                                        <input type="number" step="0.01" class="form-control"  value="{{ old('amount')}}"  name="amount" required>
                                        @if ($errors->has('amount'))
                                            <span class="help-block">
											 <strong>
                                                 Sinf no'g'ri shaklda kiritilgan</strong>
										   </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="certificate row">
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('party_number') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label ">To'da p/x №</label>
                                        <input type="text" class="form-control" maxlength="10" value="{{ old('party_number')}}"  name="party_number">
                                        @if ($errors->has('party_number'))
                                            <span class="help-block">
											 <strong>
                                                 To'da raqami noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('nav') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label ">Nav p/x № </label>
                                        <input type="number" class="form-control" max="6" value="{{ old('nav')}}"  name="nav">
                                        @if ($errors->has('nav'))
                                            <span class="help-block">
											 <strong>
                                                 Nav noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('nav') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label ">Sinf p/x №</label>
                                        <input type="number" class="form-control" max="6" value="{{ old('sinf')}}"  name="sinf" required>
                                        @if ($errors->has('sinf'))
                                            <span class="help-block">
											 <strong>
                                                 Sinf no'g'ri shaklda kiritilgan</strong>
										   </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="certificate row">
                                    <div class="col-md-6 row">
                                        <label for="number" class="form-label ">Shtrix kod raqami:<label class="text-danger">*</label></label>
                                    <div class="col-md-6 form-group has-feedback {{ $errors->has('from_kod') ? ' has-error' : '' }}">
                                        <input type="number" class="form-control" maxlength="10" value="{{ old('from_kod')}}"  name="from_kod" required>
                                        <label for="number" class="form-label ">dan</label>
                                        @if ($errors->has('from_kod'))
                                            <span class="help-block">
                                                <strong class="text-danger">{{$errors->first('from_kod')}}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 form-group has-feedback {{ $errors->has('to_kod') ? ' has-error' : '' }}">
                                        <input type="number" class="form-control" value="{{ old('to_kod')}}"  name="to_kod" required>
                                        <label for="number" class="form-label ">gacha </label>
                                        @if ($errors->has('to_kod'))
                                            <span class="help-block">
                                                <strong class="text-danger">{{$errors->first('to_kod')}}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    </div>
                                    <div class="col-md-6 row">
                                        <label for="number" class="form-label ">Toylar ketma-ketligi:<label class="text-danger">*</label></label>
                                    <div class="col-md-6 form-group has-feedback {{ $errors->has('from_toy') ? ' has-error' : '' }}">
                                        <input type="number" class="form-control" maxlength="10" value="{{ old('from_toy')}}"  name="from_toy" required>
                                        <label for="number" class="form-label ">dan</label>
                                    </div>
                                    <div class="col-md-6 form-group has-feedback {{ $errors->has('to_toy') ? ' has-error' : '' }}">
                                        <input type="number" class="form-control" value="{{ old('to_toy')}}"  name="to_toy" required>
                                        <label for="number" class="form-label ">gacha </label>
                                        @if ($errors->has('to_toy'))
                                            <span class="help-block">
                                                <strong class="text-danger">{{$errors->first('to_toy')}}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    </div>
                                </div>
								<div class="form-group col-md-12 col-sm-12">
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
	<script src="{{ URL::asset('vendors/moment/min/moment.min.js') }}"></script>
	<script src="{{ URL::asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
	<script src="{{ URL::asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
<script type="text/javascript">
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
        }, 5000);
    }
</script>
@endsection

