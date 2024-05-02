@extends('layouts.app')
@section('content')
<!-- page content -->
<?php $userid = Auth::user()->id; ?>
@can('create', \App\Models\Application::class)
   <div class="section">
		<div class="page-header">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<i class="fe fe-life-buoy mr-1"></i>&nbsp Qaror shakillantirish
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
										<a href="{!! url('/decision/search')!!}">
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
						<form id="invoice-form" method="post" action="{!! url('decision/store') !!}" enctype="multipart/form-data"
                              data-parsley-validate class="form-horizontal form-label-left">
                            @csrf
							<div class="row" >

								<input type="hidden" name="_token" value="{{csrf_token()}}">
                                <input type="hidden"  name="app_id" value="{{ $app->id}}" >
                                <div class="col-md-4 form-group has-feedback">
                                    <label class="form-label" for="app_number">Arizachi nomi <label class="text-danger">*</label></label>
                                    <input type="text" readonly name="app_number" value="{{ optional($app->organization)->name }}" class="form-control">
                                </div>
                                <div class="col-md-4 form-group has-feedback">
                                    <label class="form-label" for="app_number">Mahsulot nomi <label class="text-danger">*</label></label>
                                    <input type="text" readonly name="app_number" value="{{ optional($app->crops)->name->name}}" class="form-control">
                                </div>
                                <div class="col-md-4 form-group has-feedback">
                                    <label class="form-label" for="app_number">Ariza sanasi <label class="text-danger">*</label></label>
                                    <input type="text" readonly name="app_number" value="{{ $app->date}}" class="form-control">
                                </div>
                                <div class="col-md-4 form-group has-feedback {{ $errors->has('number') ? ' has-error' : '' }}">
                                    <label for="middle-name" class="form-label">Buyruq raqami <label class="text-danger">*</label></label>
                                    <input type="text" class="form-control" maxlength="25" value="{{ old('number')}}"  name="number">
                                    @if ($errors->has('number'))
                                        <span class="help-block">
											 <strong>Buyruq raqami noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                    @endif
                                </div>
                                <div class="col-md-4 form-group {{ $errors->has('dob') ? ' has-error' : '' }}">
                                    <label class="form-label">Buyruq sanasi sanasi <label class="text-danger">*</label></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                            </div>
                                        </div>
                                        <input type="text" id="date_of_birth" class="form-control dob" placeholder="<?php echo getDatepicker();?>" name="dob" value="{{ old('dob') }}" onkeypress="return false;" required />
                                        @if(!empty($customer))
                                            value="{{$customer->d_o_birth}}"
                                        @endif
                                    </div>
                                    @if ($errors->has('dob'))
                                        <span class="help-block">
											<strong style="margin-left:27%;">Buyruq sanasi noto'g'ti shaklda kiritilgan</strong>
										</span>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group overflow-hidden">
                                        <label class="form-label">Laboratoriya<label class="text-danger">*</label></label>
                                        <select class="w-100 form-control" name="laboratory_id" required>
                                            @if(count($directors))
                                                <option value="">Laboratoriyani tanlang</option>
                                            @endif
                                            @foreach($laboratories as $laboratory)
                                                <option value="{{$laboratory->id}}" @if($laboratory->id == old('laboratory_id')) selected @endif
                                                > {{$laboratory->name}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
								<div class="form-group col-md-12 col-sm-12">
									<div class="col-md-12 col-sm-12 text-center">
										<a class="btn btn-primary" href="{{ URL::previous() }}">{{trans("app.Ortga")}}</a>
										<button type="submit" id="invoice-form-submitter" class="btn btn-success">{{ trans('app.Submit')}}</button>
                                        <a class="btn btn-success disabled d-none" id="test_program" href="{!! url('/tests/add/'.$app->id) !!}">Sinov dasturi qo'shish</a>
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

@endsection
@section('scripts')
    <script>
        $("input.dob").datetimepicker({
            format: "dd-mm-yyyy",
            autoclose: 1,
            minView: 2,
            startView:'decade',
            endDate: new Date(),
        });

    </script>
@endsection
