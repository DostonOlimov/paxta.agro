@extends('layouts.front')
@section('content')
<!-- page content -->
	   <div class="section">
			<div class="page-header">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">
						<i class="fe fe-life-buoy mr-1"></i>&nbsp Arizalar
					</li>
				</ol>
			</div>
           @can('myupdate', $app)
			<div class="row">
				<div class="col-md-12">
					<div class="card">
						<div class="card-body">
							<div class="panel panel-primary">
								<div class="tab_wrapper page-tab">
									<ul class="tab_list">
										<li>
											<a href="{!! url('/application/my-applications')!!}">
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
                                    <div class="col-md-4">
                                        <div class="form-group overflow-hidden">
                                            <label class="form-label">Ekin turi<label
                                                    class="text-danger">*</label></label>
                                            <select class="w-100 form-control state_of_country custom-select"  id="crops_name" name="name"
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
                                            <input class="form-control" id="kodtnved" type="text" name="tnved" data-field-name="tin" data-field-length="10"
                                                   minlength="10"
                                                   data-mask="0000000000" maxlength="10" required="required"
                                                   title="10ta raqam kiriting!" data-pattern-mismatch="Noto'g'ri shakl" value="{{ $app->crops->kodtnved}}"
                                            />
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
                                    <div class="col-md-4 form-group has-feedback">
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
                                    <div class="col-md-4 form-group has-feedback" style="display: none" id="pre_name">
                                        <label class="form-label">Chigit turi <label class="text-danger">*</label></label>
                                        <div class=" gender">
                                            <label class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input"  name="pre_name" value="tukli" checked required checked>
                                                <span class="custom-control-label">Tukli</span>
                                            </label>
                                            <label class="custom-control custom-radio">
                                                <input type="radio"  class="custom-control-input" name="pre_name" value="tuksiz" required>
                                                <span class="custom-control-label">Tuksiz </span>
                                            </label>
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
                <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp Ushbu arizani o'zgartirish huquqi sizda mavjud emas</span>
            </div>
        </div>
    </div>
@endcan
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="{{ URL::asset('vendors/moment/min/moment.min.js') }}"></script>
<script src="{{ URL::asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ URL::asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
<script type="text/javascript">
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

    // get kod tn ved from corn's id crops_name
    const kodtnved = document.getElementById('kodtnved');
    const stateDropdown = document.getElementById('crops_name');

    stateDropdown.addEventListener('change', () => {
        const stateId = stateDropdown.value;
        fetch(`/getkodtnved/${stateId}`)
            .then(response => response.json())
            .then(data => kodtnved.value = data.code);
    });

    //chigit uchun radio button qo'shish
    function getPreName(th) {

        corn_id = th.val();
        if (corn_id == 21) {
            document.getElementById("pre_name").style.display = "block";
        }
        else{
            document.getElementById("pre_name").style.display = "none";
        }

    }

    $('select.state_of_country').on('change', function () {
        getPreName($(this));
    });
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
        $('select.requirements').select2({
            placeholder: 'Ilovani tanlang',
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
    });
</script>

@endsection
