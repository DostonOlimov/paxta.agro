@extends('layouts.front')
@section('content')

    @if(Auth::user()->zavod_id)
        <link href="{{ asset('assets/css/formApplications.css') }}" rel="stylesheet">
        <ul class="step-wizard-list">
            <li class="step-wizard-item ">
                <span class="progress-count first-progress-bar">1</span>
                <span class="progress-label">{{trans('app.Buyurtmachi korxonani qo\'shish')}}</span>
            </li>
            <li class="step-wizard-item current-item">
                <span class="progress-count">2</span>
                <span class="progress-label">{{trans('app.Mahsulot ma\'lumotlari')}}</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">3</span>
                <span class="progress-label">{{trans('app.Yuk ma\'lumotlari')}}</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count last-progress-bar">4</span>
                <span class="progress-label">{{trans('app.Sifat ko\'rsatkichlari')}}</span>
            </li>
        </ul>

        <div class="section">

		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">

						<form method="post" action="{!! url('application/my-application-store') !!}" enctype="multipart/form-data"  class="form-horizontal upperform">
							<div class="row" >

								<input type="hidden" name="_token" value="{{csrf_token()}}">
                                <input type="hidden" name="organization" value="{{$organization}}">

                                <div class="col-md-6">
                                    <div class="form-group overflow-hidden">
                                        <label class="form-label">{{trans('app.Sertifikatlanuvchi mahsulot')}}<label
                                                class="text-danger">*</label></label>
                                        <select class="w-100 form-control name_of_corn custom-select" name="name" id="crops_name"
                                                url="{!! url('/gettypefromname') !!}">
                                            @if(count($names))
                                                <option value="">{{trans('app.Sertifikatlanuvchi mahsulot turini tanlang')}}</option>
                                            @endif
                                            @if(!empty($names))
                                                @foreach($names as $name)
                                                    <option value="{{ $name->id }}"> {{$name->name}} </option>
                                                @endforeach

                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div id="tin-container" class="col-md-6 legal-fields">
                                    <div class="form-group">
                                        <label class="form-label">{{trans('app.Kod TN VED')}}<label class="text-danger">*</label></label>
                                        <input class="form-control" id="kodtnved" type="text" name="tnved" data-field-name="tin" data-field-length="10"
                                               minlength="10"
                                               data-mask="0000000000" maxlength="10" required="required"
                                               title="10ta raqam kiriting!" data-pattern-mismatch="Noto'g'ri shakl" value="{{ old('tnved')}}"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6 form-group has-feedback {{ $errors->has('party_number') ? ' has-error' : '' }}">
                                    <label for="middle-name" class="form-label">{{trans('app.Toʼda (partiya) raqami')}} <label class="text-danger">*</label></label>
                                    <input type="text" class="form-control" maxlength="25"  name="party_number" value="{{ old('party_number')}}">
                                    @if ($errors->has('party_number'))
                                        <span class="help-block">
											 <strong>Partiya raqami noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                    @endif
                                </div>
                                <div class="col-md-6 form-group has-feedback {{ $errors->has('party_number2') ? ' has-error' : '' }}">
                                    <label for="middle-name" class="form-label">{{trans('app.Dublikat raqami')}} <label class="text-danger">*</label></label>
                                    <input type="text" class="form-control" maxlength="25"  name="party_number2" value="{{ old('party_number2')}}">
                                    @if ($errors->has('party_number2'))
                                        <span class="help-block">
											 <strong>Dublikat raqami noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                    @endif
                                </div>
                                <div class="col-md-6 form-group has-feedback {{ $errors->has('amount') ? ' has-error' : '' }}">
                                    <label for="middle-name" class="form-label">{{trans('app.amount')}} <label class="text-danger">*</label></label>
                                    <input type="number" step="0.01" class="form-control" maxlength="25" value="{{ old('amount')}}"  name="amount">
                                    @if ($errors->has('amount'))
                                        <span class="help-block">
											 <strong>Sertifikatlanuvchi mahsulot miqdori noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group overflow-hidden">
                                        <label class="form-label">{{trans('app.O\'lchov turi')}} <label class="text-danger">*</label></label>
                                        <select class="w-100 form-control" name="measure_type">
                                            @if(count($measure_types))
                                                <option value="">{{trans('app.O\'lchov turini tanlang')}}</option>
                                            @endif
                                            @foreach($measure_types as $key=>$name)
                                                <option value="{{ $key }}"   @if($key == old('measure_type') or $key == 2) selected @endif
                                                > {{$name}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

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
@endif
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
    function getTypeOfCorn(th) {

        corn_id = th.val();

        var url = th.attr('url');

        // get array of types from corn's id
        $.ajax({
            type: 'GET',
            url: url,
            data: {
                name_id: corn_id,
            },
            success: function (response) {
                var typeMenu = $('select.type_of_corn')
                var customerType = typeMenu.attr('val');
                typeMenu.html(response);

                if (customerType) {
                    typeMenu.find('option[value="' + customerType + '"]').attr('selected', 'selected');
                }

            }

        });

        // get array of generation from corn's id
        $.ajax({
            type: 'GET',
            url: "{!! url('/getgenerationfromname') !!}",
            data: {
                name_id: corn_id,
            },
            success: function (response) {
                var typeMenu = $('select.type_of_corn2')
                var customerType = typeMenu.attr('val');
                typeMenu.html(response);

                if (customerType) {
                    typeMenu.find('option[value="' + customerType + '"]').attr('selected', 'selected');
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

    $('select.name_of_corn').on('change', function () {
        getPreName($(this));
    });

    $('select.name_of_corn').on('change', function () {
        getTypeOfCorn($(this));
    });

    if ($('select.type_of_corn').attr('val')) {
        getTypeOfCorn($('select.name_of_corn'));
    }
    if ($('select.type_of_corn2').attr('val')) {
        getTypeOfCorn($('select.name_of_corn'));
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
            corn_id = $(this).val();
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