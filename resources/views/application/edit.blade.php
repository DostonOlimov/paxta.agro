@extends('layouts.app')
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
           @can('update', $app)
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
							<form method="post" action="/application/update/{{ $app->id }}" enctype="multipart/form-data" class="form-horizontal upperform">
                                <div class="row" >
                                    {!! method_field('patch') !!}
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <div class="col-md-6">
                                        <div class="form-group" >
                                            <label class="form-label" for="organization">
                                                {{trans('app.Buyurtmachi korxona yoki tashkilot nomi')}} <span class="text-danger">*</span>
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
                                                <a class="btn btn-primary" href="{!! url('/organization/add/2')!!}">{{ trans('app.Qo\'shish')}}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 row">
                                        <div class="form-group" >
                                            <label class="form-label" for="prepared">
                                                {{trans('app.Tayorlangan shaxobcha yoki sexning nomi')}} <span class="text-danger">*</span>
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
                                                <a class="btn btn-primary" href="{!! url('/prepared/add/2')!!}">{{ trans('app.Qo\'shish')}}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 form-group {{ $errors->has('dob') ? ' has-error' : '' }}">
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
                                        @if ($errors->has('dob'))
                                            <span class="help-block">
											<strong style="margin-left:27%;">Ariza sanasi noto'g'ti shaklda kiritilgan</strong>
										</span>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group overflow-hidden">
                                            <label class="form-label">{{trans('app.Sertifikatlanuvchi mahsulot')}}<label
                                                    class="text-danger">*</label></label>
                                            <select class="w-100 form-control state_of_country custom-select"  id="crops_name" name="name"
                                                    url="{!! url('/gettypefromname') !!}">
                                                @if(count($names))
                                                    <option value="">{{trans('app.Sertifikatlanuvchi mahsulot')}}</option>
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
                                    <div id="tin-container" class="col-md-4 legal-fields">
                                        <div class="form-group">
                                            <label class="form-label">{{trans('app.Kod TN VED')}}<label class="text-danger">*</label></label>
                                            <input class="form-control" id="kodtnved" type="text" name="tnved" data-field-name="tin" data-field-length="10"
                                                   minlength="10"
                                                   data-mask="0000000000" maxlength="10" required="required"
                                                   title="10ta raqam kiriting!" data-pattern-mismatch="Noto'g'ri shakl" value="{{ $app->crops->kodtnved}}"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group overflow-hidden">
                                            <label class="form-label">{{trans('app.Ishlab chiqargan davlat')}}<label
                                                    class="text-danger">*</label></label>
                                            <select class="w-100 form-control" name="country" required>
                                                @if(count($countries))
                                                    <option value="">{{trans('app.Mamlakat nomini tanlang')}}</option>
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
                                        <label for="middle-name" class="form-label">{{trans('app.Toʼda (partiya) raqami')}} <label class="text-danger">*</label></label>
                                        <input type="text" class="form-control" maxlength="25"  name="party_number" value="{{ $app->crops->party_number}}" >
                                        @if ($errors->has('party_number'))
                                            <span class="help-block">
											 <strong>Partiya raqami noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group overflow-hidden">
                                            <label class="form-label">{{trans('app.Hosil yili')}}<label class="text-danger">*</label></label>
                                            <select class="w-100 form-control" name="year" required>
                                                @if(count($year))
                                                    <option value="">{{trans('app.Hosil yilini tanlang')}}</option>
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
                                    <div class="col-md-4">
                                        <div class="form-group overflow-hidden">
                                            <label class="form-label">{{trans('app.O\'lchov turi')}} <label class="text-danger">*</label></label>
                                            <select class="w-100 form-control" name="measure_type" >
                                                @if(count($measure_types))
                                                    <option value="">{{trans('app.O\'lchov turini tanlang')}}</option>
                                                @endif
                                                @foreach($measure_types as $key=>$name)
                                                    <option value="{{ $key }}"   @if($key == $app->crops->measure_type) selected @endif
                                                    > {{$name}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('amount') ? ' has-error' : '' }}">
                                        <label for="middle-name" class="form-label">{{trans('app.amount')}} <label class="text-danger">*</label></label>
                                        <input type="number" step="0.01" class="form-control" maxlength="25" value="{{ $app->crops->amount}}"  name="amount" >
                                        @if ($errors->has('amount'))
                                            <span class="help-block">
											 <strong>Mahsulot miqdori noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('toy_count') ? ' has-error' : '' }}">
                                        <label for="middle-name" class="form-label">{{trans('app.Toylar soni')}} <label class="text-danger">*</label></label>
                                        <input type="number" class="form-control" maxlength="25" value="{{ $app->crops->toy_count}}"  name="toy_count" >
                                        @if ($errors->has('toy_count'))
                                            <span class="help-block">
											 <strong>Mahsulot toy soni noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4" style="display: none">
                                        <div class="form-group overflow-hidden">
                                            <label class="form-label">Sertifikatlashtirish sxemasini tanlang<label class="text-danger">*</label></label>
                                            <select class="w-100 form-control" name="sxeme_number">
                                                <option value="7" @if( $app->crops->sxeme_number == 7) selected @endif> 7</option>
                                                <option value="3" @if( $app->crops->sxeme_number == 3) selected @endif> 3</option>
                                            </select>
                                        </div>
                                    </div>
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
@else
    <div class="section" role="main">
        <div class="card">
            <div class="card-body text-center">
                <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp {{trans('app.Ushbu arizani o\'zgartirish huquqi sizda mavjud emas')}}</span>
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
    // get kod tn ved from corn's id crops_name
    const kodtnved = document.getElementById('kodtnved');
    const stateDropdown = document.getElementById('crops_name');

    stateDropdown.addEventListener('change', () => {
        const stateId = stateDropdown.value;
        if(stateId){
            fetch(`/getkodtnved/${stateId}`)
                .then(response => response.json())
                .then(data => kodtnved.value = data.code);
        }
    });
</script>

@endsection
