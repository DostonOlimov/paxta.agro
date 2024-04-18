@extends('layouts.app')
@section('styles')
    <style>
        .form-group label {
            display: inline-block;
            margin-bottom: 0; /* Optionally remove any bottom margin */
        }
        .form-group input {
            display: inline-block;
            width: calc(100% - 40px); /* Adjust the width as needed */
            margin-left: 10px; /* Add some space between the label and input */
        }
    </style>
@endsection
@section('content')
<!-- page content -->
<?php $userid = Auth::user()->id;?>
@can('create', \App\Models\Application::class)
   <div class="section">
		<div class="page-header">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans('message.In Xaus ma\'lumotlari')}}
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
										<a href="{!! url('/in_xaus/list')!!}">
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
						<form method="post" action="{!! url('in_xaus/store') !!}" enctype="multipart/form-data"  class="form-horizontal upperform">
							<div class="row" >
								<input type="hidden" name="_token" value="{{csrf_token()}}">
                                <hr>
                                <div class="col-md-6 form-group {{ $errors->has('date') ? ' has-error' : '' }}">
                                    <label class="form-label">{{trans('app.In xaus aniqlangan sana')}} <label class="text-danger">*</label></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                            </div>
                                        </div>
                                        <input type="text" id="date_of_birth" class="form-control date" placeholder="<?php echo getDatepicker();?>" name="date" value="{{ old('date') }}" onkeypress="return false;" required />
                                        @if(!empty($customer))
                                            value="{{$customer->d_o_birth}}"
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 has-feedback pb-1" >
                                    <span class="btn btn-danger" style="width: 100%" >{{ "Mikroner"}}</span>
                                </div>
                                <div class="col-md-3 has-feedback">
                                    <span class="btn btn-success" style="width: 100%" >{{ "Strength"}}</span>
                                </div>
                                <div class="col-md-3 has-feedback">
                                    <span class="btn btn-info" style="width: 100%" >{{ "Iniformity"}}</span>
                                </div>
                                <div class="col-md-3 has-feedback">
                                    <span class="btn btn-warning" style="width: 100%" >{{ "Length"}}</span>
                                </div>
                                @for($i = 1; $i < 11; $i++)
                                    <div class="col-md-3 form-group has-feedback ">
                                        <label>{{$i}}</label>
                                        <input  type="number" step="0.001" class="form-control" value="{{ old('mic'.$i)}}"  name="mic{{$i}}" required>
                                    </div>
                                    <div class="col-md-3 form-group has-feedback">
                                        <label>{{$i}}</label>
                                        <input  type="number" step="0.001" class="form-control" value="{{ old('str'.$i)}}"  name="str{{$i}}" required>
                                    </div>
                                    <div class="col-md-3 form-group has-feedback">
                                        <label>{{$i}}</label>
                                        <input  type="number" step="0.001" class="form-control" value="{{ old('inf'.$i)}}"  name="inf{{$i}}" required>
                                    </div>
                                    <div class="col-md-3 form-group has-feedback">
                                        <label>{{$i}}</label>
                                        <input  type="number" step="0.001" class="form-control" value="{{ old('len'.$i)}}"  name="len{{$i}}" required>
                                    </div>
                                @endfor
                                    <div class="form-group col-md-12 col-sm-12">
									<div class="col-md-12 col-sm-12 text-center">
										<a class="btn btn-primary" href="{{ URL::previous() }}">{{ trans('app.Cancel')}}</a>
										<button type="submit" class="btn btn-success"  onclick="disableButton()" id="submitter">{{ trans('app.Submit')}}</button>
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
        }, 2000);
    }

</script>

  @endsection
