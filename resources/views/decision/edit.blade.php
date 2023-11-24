@extends('layouts.app')
@section('content')
<!-- page content -->
<?php $userid = Auth::user()->id; ?>
@can('create', \App\Models\Application::class)
	   <div class="section">
			<div class="page-header">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">
						<i class="fe fe-life-buoy mr-1"></i>&nbsp Arizalar
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
											<a href="{!! url('/application/list')!!}">
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
                                    {!! method_field('patch') !!}
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('app_number') ? ' has-error' : '' }}">
                                        <label class="form-label" for="app_number">Ariza raqami <label class="text-danger">*</label></label>
                                        <div class="">
                                            <input type="number" id="app_number" name="app_number" value="{{ $app->app_number}}" class="form-control" maxlength="20" required>
                                            @if ($errors->has('app_number'))
                                                <span class="help-block">
											<strong>Ariza raqami noto'g'ti shaklda kiritilgan yoki oldindan mavjud</strong>
										</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 form-group {{ $errors->has('dob') ? ' has-error' : '' }}">
                                        <label class="form-label">Ariza sanasi <label class="text-danger">*</label></label>
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
                                        @if ($errors->has('dob'))
                                            <span class="help-block">
											<strong style="margin-left:27%;">Ariza sanasi noto'g'ti shaklda kiritilgan</strong>
										</span>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group overflow-hidden">
                                            <label class="form-label">Ariza turi<label class="text-danger">*</label></label>
                                            <select class="w-100 form-control" name="type" required>
                                                @if(count($type))
                                                    <option value="">Ariza turini tanlang</option>
                                                @endif
                                                @foreach($type as $key=>$name)
                                                    <option value="{{ $key }}" @if($key == $app->type)  selected @endif
                                                    > {{$name}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" >
                                            <label class="form-label" for="organization">
                                                Buyurtmachi korxona yoki tashkilot nomi <span class="text-danger">*</span>
                                            </label>
                                            <select id="organization"
                                                    class="form-control owner_search" name="organization" required>
                                                @if(!empty($app))
                                                    <option selected
                                                            value="{{ $app->organization_id }}"
                                                    >{{optional($app->organization)->name}}</option>
                                                @endif
                                            </select>
                                            <div>
                                                <a class="btn btn-primary" href="{!! url('/organization/add')!!}">{{ trans('app.Qo\'shish')}}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 row">
                                        <div class="form-group" >
                                            <label class="form-label" for="prepared">
                                                Urugʼlik tayorlangan shaxobcha yoki sexning nomi <span class="text-danger">*</span>
                                            </label>
                                            <select id="prepared" required
                                                    class="form-control owner_search2" name="prepared">
                                                @if(!empty($app))
                                                    <option selected
                                                            value="{{ $app->prepared_id }}"
                                                    >{{optional($app->prepared)->name}}</option>
                                                @endif
                                            </select>
                                            <div>
                                                <a class="btn btn-primary" href="{!! url('/prepared/add')!!}">{{ trans('app.Qo\'shish')}}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group overflow-hidden">
                                            <label class="form-label">Ekin turi<label
                                                    class="text-danger">*</label></label>
                                            <select class="w-100 form-control state_of_country custom-select" name="name"
                                                    url="{!! url('/gettypefromname') !!}">
                                                @if(count($names))
                                                    <option value="">Ekin turini tanlang</option>
                                                @endif
                                                @if(!empty($names))
                                                    @foreach($names as $name)
                                                        <option value="{{ $name->id }}" @if($name->id == $app->crops->name_id) selected @endif
                                                        > {{$name->name}} </option>
                                                    @endforeach

                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 form-group overflow-hidden">
                                        <label class="form-label">Urug' navi
                                            <label class="text-danger">*</label></label>
                                        <div class="row">
                                            <div class="col-12">
                                                <select class="form-control w-100 city_of_state custom-select" name="type"
                                                        required="">
                                                    @if(isset($app->crops->type)) <option value="{{$app->crops->type_id}}">{{$app->crops->type->name}}</option> @endif
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-4 form-group overflow-hidden">
                                        <label class="form-label">Urug' avlodi
                                            <label class="text-danger">*</label></label>
                                        <div class="row">
                                            <div class="col-12">
                                                <select class="form-control w-100 city_of_state2 custom-select2" name="generation" required="">
                                                    @if(isset($app->crops->generation)) <option value="{{$app->crops->generation_id}}">{{$app->crops->generation->name}}</option> @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tin-container" class="col-md-4 legal-fields">
                                        <div class="form-group">
                                            <label class="form-label">Kod TN VED<label class="text-danger">*</label></label>
                                            <input class="form-control" type="text" name="tnved" data-field-name="tin" data-field-length="10"
                                                   minlength="10"
                                                   data-mask="0000000000" maxlength="10" required="required"
                                                   title="10ta raqam kiriting!" data-pattern-mismatch="Noto'g'ri shakl" value="{{ $app->crops->kodtnved}}"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group overflow-hidden">
                                            <label class="form-label">Ishlab chiqarilgan mamlakat<label
                                                    class="text-danger">*</label></label>
                                            <select class="w-100 form-control" name="country" required>
                                                @if(count($countries))
                                                    <option value="">Mamlakat nomini tanlang</option>
                                                @endif
                                                @if(!empty($countries))
                                                    @foreach($countries as $name)
                                                        <option value="{{ $name->id }}"  @if($name->id == $app->crops->country_id) selected @endif
                                                        > {{$name->name}} </option>
                                                    @endforeach

                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('party_number') ? ' has-error' : '' }}">
                                        <label for="middle-name" class="form-label">Partiya raqami <label class="text-danger">*</label></label>
                                        <input type="text" class="form-control" maxlength="25"  name="party_number" value="{{ $app->crops->party_number}}" required>
                                        @if ($errors->has('party_number'))
                                            <span class="help-block">
											 <strong>Partiya raqami noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group overflow-hidden">
                                            <label class="form-label">O'lchov turi <label class="text-danger">*</label></label>
                                            <select class="w-100 form-control" name="measure_type" required>
                                                @if(count($measure_types))
                                                    <option value="">O'lchov turini tanlang</option>
                                                @endif
                                                @foreach($measure_types as $key=>$name)
                                                    <option value="{{ $key }}"   @if($key == $app->crops->measure_type) selected @endif
                                                    > {{$name}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('amount') ? ' has-error' : '' }}">
                                        <label for="middle-name" class="form-label">Ekin miqdori <label class="text-danger">*</label></label>
                                        <input type="number" step="0.01" class="form-control" maxlength="25" value="{{ $app->crops->amount}}"  name="amount" required>
                                        @if ($errors->has('amount'))
                                            <span class="help-block">
											 <strong>Ekin miqdori noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group overflow-hidden">
                                            <label class="form-label">Hosil yili<label class="text-danger">*</label></label>
                                            <select class="w-100 form-control" name="year" required>
                                                @if(count($year))
                                                    <option value="">Hosil yilini tanlang</option>
                                                @endif
                                                @foreach($year as $key=>$name)
                                                    <option value="{{ $key }}"
                                                            @if($key == $app->crops->year) selected @endif
                                                    >{{$name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group has-feedback">
                                        <label class="form-label"> Ishlab chiqarish turi</label>
                                        <div class="">
                                            <select required class="form-control crop_production" name="state[]" multiple="multiple" >
                                                @if(!empty($production_type))
                                                    @foreach($production_type as $state)
                                                        <option value="{{$state->id}}">{{$state->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group has-feedback">
                                        <label class="form-label" for="data">Qo'shimcha ma'lumotlar<label class="text-danger">*</label></label>
                                        <div class="">
                                            <textarea id="data" name="data" class="form-control" maxlength="100" >{{ $app->data}}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12 col-sm-12">
                                        <div class="col-md-12 col-sm-12 text-center">
                                            <a class="btn btn-primary" href="{{ URL::previous() }}">{{ trans('app.Cancel')}}</a>
                                            <button type="submit" class="btn btn-success">{{ trans('app.Update')}}</button>
                                        </div>
                                    </div>
                                </div>
							</form>
						</div>
					</div>
				</div>
				</div>
			</div>
		</div>
@else
	<div class="right_col" role="main">
		<div class="nav_menu main_title" style="margin-top:4px;margin-bottom:15px;">
            <div class="nav toggle" style="padding-bottom:16px;">
               <span class="titleup">&nbsp {{ trans('app.You are not authorize this page.')}}</span>
            </div>
        </div>
	</div>

@endcan
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
</script>

<script>
    $(document).ready(function(){

        $('.datepicker1').datetimepicker({
            format: "<?php echo getDatepicker(); ?>",
            autoclose: 1,
            minView: 2,
            endDate: new Date(),
        });

        $(".datepicker,.input-group-addon").click(function(){
            var dateend = $('#left_date').val('');

        });

        $(".datepicker").datetimepicker({
            format: "<?php echo getDatepicker(); ?>",
            minView: 2,
            autoclose: 1,
        }).on('changeDate', function (selected) {
            var startDate = new Date(selected.date.valueOf());

            $('.datepicker2').datetimepicker({
                format: "<?php echo getDatepicker(); ?>",
                minView: 2,
                autoclose: 1,

            }).datetimepicker('setStartDate', startDate);
        })
            .on('clearDate', function (selected) {
                $('.datepicker2').datetimepicker('setStartDate', null);
            })

        $('.datepicker2').click(function(){

            var date = $('#join_date').val();
            if(date == '')
            {
                swal('First Select Join Date');
            }
            else{
                $('.datepicker2').datetimepicker({
                    format: "<?php echo getDatepicker(); ?>",
                    minView: 2,
                    autoclose: 1,
                })

            }
        });
    });

</script>
<script>
    $(document).ready(function () {
        $('select.owner_search').select2({
            ajax: {
                url: '/organization/search_by_name',
                delay: 300,
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term
                    }
                },
                processResults: function (data) {
                    data = data.map((name, index) => {
                        return {
                            id: name.id,
                            text: capitalize(name.name + (name.name ? ' - STiR:' + name.inn : ''))
                        }
                    });
                    return {
                        results: data
                    }
                }
            },
            language: {
                inputTooShort: function () {
                    return 'Korxona (nomi), STIR ini kiritib izlang';
                },
                searching: function () {
                    return 'Izlanmoqda...';
                },
                noResults: function () {
                    return "Natija topilmadi"
                },
                errorLoading: function () {
                    return "Natija topilmadi"
                }
            },
            placeholder: 'Korxona nomini kiriting',
            minimumInputLength: 2
        })
        $('select.owner_search2').select2({
            ajax: {
                url: '/prepared/search_by_name',
                delay: 300,
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term
                    }
                },
                processResults: function (data) {
                    data = data.map((name, index) => {
                        return {
                            id: name.id,
                            text: capitalize(name.name )
                        }
                    });
                    return {
                        results: data
                    }
                }
            },
            language: {
                inputTooShort: function () {
                    return 'Korxona nomini kiritib izlang';
                },
                searching: function () {
                    return 'Izlanmoqda...';
                },
                noResults: function () {
                    return "Natija topilmadi"
                },
                errorLoading: function () {
                    return "Natija topilmadi"
                }
            },
            placeholder: 'Korxona nomini kiriting',
            minimumInputLength: 2
        })
        function capitalize(text) {
            var words = text.split(' ');
            for (var i = 0; i < words.length; i++) {
                if (words[i][0] == null) {
                    continue;
                } else {
                    words[i] = words[i][0].toUpperCase() + words[i].substring(1).toLowerCase();
                }

            }
            return words.join(' ');
        }
    });
</script>
<script >
    $(document).ready(function () {
        $('.states').select2({
            minimumResultsForSearch: Infinity
        });
    })
    function getCitiesOfState(th) {

        stateid = th.val();

        var url = th.attr('url');

        $.ajax({
            type: 'GET',
            url: url,
            data: {
                name_id: stateid,
            },
            success: function (response) {
                var citiesMenu = $('select.city_of_state')
                var customerCity = citiesMenu.attr('val');
                citiesMenu.html(response);

                if (customerCity) {
                    citiesMenu.find('option[value="' + customerCity + '"]').attr('selected', 'selected');
                }

            }

        });
        $.ajax({
            type: 'GET',
            url: "{!! url('/getgenerationfromname') !!}",
            data: {
                name_id: stateid,
            },
            success: function (response) {
                var citiesMenu = $('select.city_of_state2')
                var customerCity = citiesMenu.attr('val');
                citiesMenu.html(response);

                if (customerCity) {
                    citiesMenu.find('option[value="' + customerCity + '"]').attr('selected', 'selected');
                }

            }

        });
    }

    $('select.state_of_country').on('change', function () {
        getCitiesOfState($(this));
    });

    if ($('select.city_of_state').attr('val')) {
        getCitiesOfState($('select.state_of_country'));
    }
    if ($('select.city_of_state2').attr('val')) {
        getCitiesOfState($('select.state_of_country'));
    }

</script>
<script>
    $(document).ready(function(){
        $('select.crop_production').select2({
            placeholder: 'Ishlab chiqarish turini tanlang',
            minimumResultsForSearch: Infinity,
            language:{
                inputTooShort:function(){
                    return 'Ma\'lumot kiritib izlang';
                },
                searching:function(){
                    return 'Izlanmoqda...';
                },
                noResults:function(){
                    return "Natija topilmadi"
                }
            }
        });
        $('body').on('change','.crop_production',function(){
            stateid = $(this).val();
            var url = $(this).attr('stateurl');
        });
    });
</script>

@endsection
