@extends('layouts.front')
@section('content')
     <style>
            @media screen and (max-width: 768px) {
                main {
                    margin: 100px 0 !important;
                }

                .my_header .navbar {
                    padding: 10px 0;
                    align-items: center;
                }

                .my_header .nav-logo {
                    margin-right: 0px;
                }

                .right-side-header {
                    column-gap: 13px;
                }

                h4 {
                    max-width: 220px;
                    font-size: 14px;
                }
            }
        </style>
        <link href="{{ asset('assets/css/formApplications.css') }}" rel="stylesheet">
        <ul class="step-wizard-list">
            <li class="step-wizard-item current-item">
                <span class="progress-count first-progress-bar">1</span>
                <span class="progress-label">{{ trans('app.Buyurtmachi korxonani qo\'shish') }}</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">2</span>
                <span class="progress-label">{{ trans('app.Mahsulot ma\'lumotlari') }}</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count">3</span>
                <span class="progress-label">{{ trans('app.Yuk ma\'lumotlari') }}</span>
            </li>
            <li class="step-wizard-item">
                <span class="progress-count last-progress-bar">4</span>
                <span class="progress-label">{{ trans('app.Sifat ko\'rsatkichlari') }}</span>
            </li>
        </ul>
        <div class="section">
            <div class="page-header1">
                <ol class="breadcrumb">
                </ol>
            </div>
            <div class="clearfix"></div>
            @if (session('message'))
                <div class="row massage">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="alert alert-success danger text-center">

                            <label for="checkbox-10 colo_success"> {{ trans('app.Duplicate Data') }} </label>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ url('/organization/my-organization-store') }}" method="post" id="myForm"
                            enctype="multipart/form-data" class="form-horizontal form-label-left">
                            <div class="row">
                                <div id="tin-container" class="form__organization__field legal-fields">
                                    <div class="form-group">
                                        <label
                                            class="form-label">{{ trans('app.Buyurtmachi korxona yoki tashkilot  STIRi') }}<label
                                                class="text-danger">*</label></label>
                                        <input class="form-control" type="text" id="stir" name="inn"
                                            value="@if ($company) {{ $company->inn }} @elseif($user->inn) {{ $user->inn }} @endif"
                                            url="{!! url('/getcompany') !!}" data-field-name="tin" data-field-length="9"
                                            placeholder="{{ trans('app.STIR') }}" minlength="9" data-mask="000000000"
                                            maxlength="9" required="required" title="9ta raqam kiriting!"
                                            data-pattern-mismatch="Noto'g'ri shakl" />
                                        @if ($errors->has('inn'))
                                            <span class="help-block">
                                                <strong class="hf-warning">{{ $errors->first('inn') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form__organization__field">
                                    <div class="form-group">
                                        <label class="form-label" for="first-name">{{ trans('app.Korxona nomi') }} <label
                                                class="text-danger">*</label>
                                        </label>
                                        <input type="text" required="required" name="name" id="name"
                                            value=" @if ($company) {{ $company->name }} @elseif($user->inn) {{ $user->company_name }} @endif"
                                            class="form-control">
                                        @if ($errors->has('name'))
                                            <span class="help-block">
                                                <strong class="hf-warning">{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form__organization__field">
                                    <div class="form-group">
                                        <label class="form-label"
                                            for="owner_name">{{ trans('app.Raxbarning ismi-sharifi') }} <label
                                                class="text-danger">*</label>
                                        </label>
                                        <input type="text" required="required" name="owner_name" id="owner_name"
                                            value="@if ($company) {{ $company->owner_name }} @elseif($user->inn) {{ $user->name . ' ' . $user->lastname . ' ' . $user->display_name }} @endif"
                                            class="form-control">
                                        @if ($errors->has('owner_name'))
                                            <span class="help-block">
                                                <strong class="hf-warning">{{ $errors->first('owner_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div
                                    class="form__organization__field has-feedback {{ $errors->has('phone_number') ? ' has-error' : '' }}">
                                    <div class="form-group">
                                        <label class="form-label">{{ trans('app.Mobile num') }}</label>
                                        <input type="text" name="phone_number"
                                            placeholder="{{ trans('app.Enter Mobile No') }}" id="phone_number"
                                            class="form-control" data-pattern-mismatch="Telefon raqamni kiriting"
                                            required="required"
                                            @if ($company) value="{{ $company->phone_number }}" @endif />

                                        @if ($errors->has('phone_number'))
                                            <span class="help-block">
                                                <strong class="hf-warning">{{ $errors->first('phone_number') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form__organization__field">
                                    <div class="form-group overflow-hidden">
                                        <label class="form-label">{{ trans('app.Region') }}<label
                                                class="text-danger">*</label></label>
                                        <select class="w-100 form-control state_of_country custom-select" name="state_id"
                                            id="state" url="{!! url('/getcityfromstate') !!}">
                                            @if (count($states))
                                                <option value="" disabled selected>{{ trans('app.Viloyat tanlang') }}
                                                </option>
                                            @endif
                                            @if (!empty($states))
                                                @foreach ($states as $state)
                                                    <option value="{{ $state->id }}"
                                                        @if (($company && optional($company->city)->state_id == $state->id) || count($states) == 1) selected="selected"
                                        @elseif($user->inn and $user->state_id == $state->id) selected="selected" @endif>
                                                        {{ $state->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('state_id'))
                                            <span class="help-block">
                                                <strong class="hf-warning">{{ $errors->first('state_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form__organization__field form-group overflow-hidden">
                                    <label class="form-label">
                                        {{ trans('app.Town/City') }}
                                        <label class="text-danger">*</label>
                                    </label>
                                    <div class="">
                                        <select class="form-control w-100 city_of_state custom-select" name="city_id"
                                            id="city2" required="">

                                            @if (!empty($cities))
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}"
                                                        @if (($company && $company->id == $city->id) || count($cities) == 1) selected="selected" @elseif($user->inn and $user->city_id == $city->id) selected="selected" @endif>
                                                        {{ $city->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('city_id'))
                                            <span class="help-block">
                                                <strong class="hf-warning">{{ $errors->first('city_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form__organization__field">
                                    <label class="form-label" for="address">{{ trans('app.Address') }}</label>
                                    <textarea
                                        class="form-control"
                                        style=""
                                        id="address"
                                        name="address"
                                        maxlength="100"
                                        required
                                        rows="2"
                                        placeholder="{{ trans('app.Enter Address') }}"
                                    >{{ old('address') }}</textarea>

                                    @if ($errors->has('address'))
                                        <span class="help-block">
                                            <strong class="hf-warning">{{ $errors->first('address') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group form-group-buttons">
                                <a class="btn btn-primary" href="{{ URL::previous() }}">{{ trans('app.Cancel') }}</a>
                                <button type="submit" class="btn btn-success">{{ trans('app.Submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/7.4.0/imask.min.js"
            integrity="sha512-8DS63sErg9A5zQEiT33fVNawEElUBRoBjCryGeufXJ82dLifenpXQDjbAM8MoTKm5NFZvtrB7DoVhOM8InOgkg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // get kod tn ved from corn's id crops_name
            let stirInput = document.getElementById('stir');

            stirInput.addEventListener('change', () => {
                let stir = stirInput.value;
                let stirUrl = "{!! url('/getcompany') !!}";
                if (stir.length === 9) {
                    $.ajax({
                        type: 'GET',
                        url: stirUrl,
                        data: {
                            stir: stir,
                        },
                        success: function(response) {
                            if (response) {
                                document.getElementById('name').value = response.name;
                                document.getElementById('owner_name').value = response
                                    .owner_name;
                                document.getElementById('phone_number').value = response
                                    .phone_number;
                                document.getElementById('address').value = response.address;
                                document.getElementById('state').value = response.state;
                                $('#city2').empty();
                                $('#city2').append($('<option>', {
                                    value: response.city,
                                    text: response.cityName
                                }));
                                //make only read inputs
                                $('#name').prop('readonly', true);
                                $('#owner_name').prop('readonly', true);
                                $('#phone_number').prop('readonly', true);
                                $('#address').prop('readonly', true);
                                $('#state').prop('disabled', true);
                                $('#city2').prop('disabled', true);
                            } else {
                                let stir =  $('#stir').val();
                                $('#myForm')[0].reset();
                                $('#name').val('');
                                $('#owner_name').val('');
                                $('#phone_number').val('');
                                $('#address').val('');
                                $('#state').val('1');
                                $('#city2').empty();
                                $('#stir').val(stir);
                                $('#name').prop('readonly', false);
                                $('#owner_name').prop('readonly', false);
                                $('#phone_number').prop('readonly', false);
                                $('#address').prop('readonly', false);
                                $('#state').prop('disabled', false);
                                $('#city2').prop('disabled', false);
                            }

                        }
                    });
                }

            });
        })

        function getCityFunction(url, stateid) {
            $.ajax({
                type: 'GET',
                url: url,

                data: {
                    stateid: stateid,
                },

                success: function(response) {

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

            getCityFunction(url, stateid);

        }
        $('select.state_of_country').on('change', function() {
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

        document.addEventListener("DOMContentLoaded", function() {
            let phoneInput = document.getElementById('phone_number');
            let phoneMask = IMask(phoneInput, {
                mask: '+{998} 00 000 00 00'
            });
            phoneInput.addEventListener('click', function() {
                if (this.value === '') {
                    this.value = '+998';
                }
            });
        });
    </script>
@endsection
