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
	   <div class="section">
			<div class="page-header">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">
						<i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans('app.Edit')}}
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
											<i class="fa fa-edit fa-lg">&nbsp;</i>
											<b>{{ trans('app.Tahrirlash')}}</b>
										</li>
									</ul>
								</div>
							</div>
							<form method="post" action="update/{{ $app->id }}" enctype="multipart/form-data" class="form-horizontal upperform">
                                <div class="row" >
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">

                                    <div class="col-md-6 form-group {{ $errors->has('dob') ? ' has-error' : '' }}">
                                        <label class="form-label">{{trans('app.Ariza sanasi')}} <label class="text-danger">*</label></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="date_of_birth" class="form-control dob" placeholder="<?php echo getDatepicker();?>"
                                                   name="dob" value="{{ date(getDateFormat(),strtotime($app->date)) }}" onkeypress="return false;" required
                                                   @if(!empty($app))
                                                   value="{{$app->date}}"
                                                @endif
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 has-feedback pb-1" >
                                        <input  type="text" class="form-control" value="Mikroner"  name="name" readonly>
                                    </div>
                                    <div class="col-md-3 has-feedback">
                                        <input  type="text" class="form-control" value="Strength"  name="name" readonly>
                                    </div>
                                    <div class="col-md-3 has-feedback">
                                        <input  type="text" class="form-control" value="Iniformity"  name="name" readonly>
                                    </div>
                                    <div class="col-md-3 has-feedback">
                                        <input  type="text" class="form-control" value="Length"  name="name" readonly>
                                    </div>
                                    @for($i = 0; $i < 10; $i++)
                                        <div class="col-md-3 form-group has-feedback ">
                                            <label>{{$i+1}}</label>
                                            <input  type="number" step="0.001" class="form-control" value="{{ $values[1][$i]->value }}"  name="mic{{$i+1}}" required>
                                        </div>
                                        <div class="col-md-3 form-group has-feedback">
                                            <label>{{$i+1}}</label>
                                            <input  type="number" step="0.001" class="form-control" value="{{ $values[2][$i]->value }}"  name="str{{$i+1}}" required>
                                        </div>
                                        <div class="col-md-3 form-group has-feedback">
                                            <label>{{$i+1}}</label>
                                            <input  type="number" step="0.001" class="form-control" value="{{ $values[3][$i]->value }}"  name="inf{{$i+1}}" required>
                                        </div>
                                        <div class="col-md-3 form-group has-feedback">
                                            <label>{{$i+1}}</label>
                                            <input  type="number" step="0.001" class="form-control" value="{{ $values[4][$i]->value }}"  name="len{{$i+1}}" required>
                                        </div>
                                    @endfor

                                    <div class="form-group col-md-12 col-sm-12">
                                        <div class="col-md-12 col-sm-12 text-center">
                                            <a class="btn btn-primary" href="{{ URL::previous() }}">{{ trans('app.Cancel')}}</a>
                                            <button type="submit" onclick="disableButton()" id="submitter" class="btn btn-success">{{ trans('app.Update')}}</button>
                                        </div>
                                    </div>
                                </div>
							</form>
						</div>
					</div>
				</div>
				</div>
			</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="{{ URL::asset('vendors/moment/min/moment.min.js') }}"></script>
<script src="{{ URL::asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ URL::asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
<script type="text/javascript">
    $("input.dob").datetimepicker({
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
        }, 1000);
    }

</script>

@endsection
