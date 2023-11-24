@extends('layouts.front')
@section('content')
    <style>
        .checkbox-success {
            background-color: #cad0cc !important;
            color: red;
        }
    </style>
    <?php $userid = Auth::user()->id; ?>
    @if(Auth::user()->role == \App\Models\User::ROLE_CUSTOMER)
            <ul class="step-wizard-list ">
                <li class="step-wizard-item current-item">
                    <span class="progress-count first-progress-bar">1</span>
                    <span class="progress-label">Buyurtmachi korxonani qo'shish</span>
                </li>
                <li class="step-wizard-item ">
                    <span class="progress-count">2</span>
                    <span class="progress-label">Ariza turini tanlash</span>
                </li>
                <li class="step-wizard-item ">
                    <span class="progress-count">3</span>
                    <span class="progress-label">Ariza ma'lumotlarini kiritish</span>
                </li>
                <li class="step-wizard-item">
                    <span class="progress-count last-progress-bar">4</span>
                    <span class="progress-label">Zaruriy hujjatlarni yuklash</span>
                </li>
            </ul>
            <div class="section">
                <div class="page-header1">
                    <ol class="breadcrumb">
                    </ol>
                </div>
                <div class="clearfix"></div>
                @if(session('message'))
                    <div class="row massage">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="alert alert-success danger text-center">

                                <label for="checkbox-10 colo_success"> {{ trans('app.Duplicate Data')}} </label>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <form action="{{ url('/organization/my-organization-store') }}" method="post" id="myForm"
                                              enctype="multipart/form-data"
                                              class="form-horizontal form-label-left">
                                            <div class="row">
                                                <div id="tin-container" class="col-md-6 legal-fields">
                                                    <div class="form-group">
                                                        <label class="form-label">Korxona STIRi<label class="text-danger">*</label></label>
                                                        <input class="form-control" type="text" id="stir" name="inn" url="{!! url('/getcompany') !!}"
                                                               data-field-name="tin" data-field-length="9"
                                                               placeholder="STIR ni kiriting" minlength="9"
                                                               data-mask="000000000" maxlength="9" required="required"
                                                               title="9ta raqam kiriting!" data-pattern-mismatch="Noto'g'ri shakl"
                                                        />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="first-name">Korxona nomi <label
                                                                class="text-danger">*</label>
                                                        </label>
                                                        <input type="text" required="required" name="name" id="name"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="owner_name">Korxona raxbarining ismi-sharifi <label
                                                                class="text-danger">*</label>
                                                        </label>
                                                        <input type="text" required="required" name="owner_name" id="owner_name"
                                                               class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-6 has-feedback {{ $errors->has('mobile') ? ' has-error' : '' }}">
                                                    <div class="form-group">
                                                        <label class="form-label">{{ trans('app.Mobile num') }}</label>
                                                        <input type="text" name="mobile" placeholder="+998 (xx) xxx-xx-xx" id="phone_number"
                                                               class="form-control" maxlength="15"
                                                               data-mask="+998 (00) 000-00-00"
                                                               data-pattern-mismatch="Telefon raqamni kiriting"
                                                               required="required"
                                                               @if(!empty($customer)) value="{{$customer->mobile}}" @endif
                                                        />

                                                        @if ($errors->has('mobile'))
                                                            <span class="help-block">
                                                    <strong>{{ $errors->first('mobile') }}</strong>
                                                </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">

                                                    <div class="form-group overflow-hidden">

                                                        <label class="form-label">{{ trans('app.Viloyat') }}<label
                                                                class="text-danger">*</label></label>

                                                        <select class="w-100 form-control state_of_country custom-select" name="state_id" id="state"
                                                                url="{!! url('/getcityfromstate') !!}">
                                                            @if(count($states))
                                                                <option value="">Viloyat tanlang</option>
                                                            @endif

                                                            @if(!empty($states))

                                                                @foreach($states as $state)

                                                                    <option value="{{ $state->id }}"

                                                                            @if( (!empty($customer_city) && $customer_city->state_id==$state->id) || count($states)==1)

                                                                            selected="selected"

                                                                        @endif

                                                                    > {{$state->name}} </option>

                                                                @endforeach

                                                            @endif

                                                        </select>

                                                    </div>

                                                </div>

                                                <div class="col-md-6 form-group overflow-hidden">

                                                    <label class="form-label">

                                                        Tuman / Shahar

                                                        <label class="text-danger">*</label>

                                                    </label>

                                                    <div class="row">

                                                        <div class="col-12">
                                                            <select class="form-control w-100 city_of_state custom-select" name="city" id="city"
                                                                    required=""
                                                                    @if(!empty($customer_city))
                                                                    val="{{$customer_city->id}}"
                                                                @endif
                                                            >
                                                                @if($cities && count($cities))
                                                                    <option value="">Viloyat tanlang</option>
                                                                @endif

                                                                @if(!empty($cities))
                                                                    @foreach($cities as $city)
                                                                        <option value="{{ $city->id }}"
                                                                                @if((!empty($customer_city) && $customer_city->id==$city->id) || count($cities)==1)
                                                                                selected="selected"
                                                                            @endif
                                                                        > {{$city->name}} </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label">{{ trans('app.Address') }}</label>
                                                    <textarea class="form-control" id="address" name="address" maxlength="100" required="required"
                                                              rows="3"
                                                              placeholder="{{ trans('app.Enter Address') }}">{{(!empty($customer))?$customer->address:''}}</textarea>
                                                </div>

                                                <input type="hidden" name="_token" value="{{csrf_token()}}">
                                                <div class="col-12 col-md-12 text-center">
                                                    <label class="form-label" style="visibility: hidden;">label</label>
                                                    <div class="form-group">
                                                        <a class="btn btn-primary"
                                                           href="{{ URL::previous() }}">{{ trans('app.Cancel')}}</a>
                                                        <button type="submit"
                                                                class="btn btn-success">{{ trans('app.Submit')}}</button>
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
    <script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // get kod tn ved from corn's id crops_name
            let stirInput = document.getElementById('stir');

            stirInput.addEventListener('change', () => {
                let stir = stirInput.value;
                let stirUrl = "{!! url('/getcompany') !!}";
                if(stir.length  === 9){
                    $.ajax({

                        type: 'GET',

                        url: stirUrl,

                        data: {

                            stir: stir,
                        },

                        success: function (response) {
                            if(response){
                                document.getElementById('name').value = response.name;
                                document.getElementById('owner_name').value = response.owner_name;
                                document.getElementById('phone_number').value = response.phone_number;
                                document.getElementById('address').value = response.address;
                                document.getElementById('state').value = response.state;
                                $('#city').empty();
                                $('#city').append($('<option>', {
                                    value: response.city,
                                    text: response.cityName
                                }));
                                //make only read inputs
                                $('#name').prop('readonly', true);
                                $('#owner_name').prop('readonly', true);
                                $('#phone_number').prop('readonly', true);
                                $('#address').prop('readonly', true);
                                $('#state').prop('disabled', true);
                                $('#city').prop('disabled', true);
                            }else{
                                let val = $('#stir').val();
                                $('#myForm')[0].reset();
                                document.getElementById('stir').value = val;
                                $('#name').value = '';
                                $('#owner_name').value = '';
                                $('#phone_number').value = '';
                                $('#address').value = '';
                                $('#state').value = 1;

                                $('#city').empty();
                                $('#name').prop('readonly', false);
                                $('#owner_name').prop('readonly', false);
                                $('#phone_number').prop('readonly', false);
                                $('#address').prop('readonly', false);
                                $('#state').prop('disabled', false);
                                $('#city').prop('disabled', false);
                            }

                        }
                    });
                }

            });
            $('.states').select2({
                minimumResultsForSearch: Infinity
            });
        })
        function getCityFunction(url,stateid) {
            $.ajax({
                type: 'GET',
                url: url,

                data: {
                    stateid: stateid,
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
        }
        function getCitiesOfState(th) {

            var stateid = th.val();
            var url = th.attr('url');

            getCityFunction(url,stateid);

        }
        $('select.state_of_country').on('change', function () {
            getCitiesOfState($(this));
        });

        if ($('select.city_of_state').attr('val')) {
            getCitiesOfState($('select.state_of_country'));
        }

    </script>
    <script>
    $(document).ready(function() {
        $('form').on('keypress', function(e) {
            if (e.keyCode == 13) {
                e.preventDefault(); // Prevent default form submission
            }
        });

        $('#submit-button').on('click', function() {
            $('form').submit(); // Submit the form when the submit button is clicked
        });
    });
</script>
@endsection
