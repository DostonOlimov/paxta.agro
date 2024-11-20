@extends('layouts.front')
@section('content')

        <style>
            @media screen and (max-width: 768px) {
                main {
                    margin: 135px 0 !important;
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
                    max-width: 155px;
                    font-size: 14px;
                }
            }
        </style>
        <link href="{{ asset('assets/css/formApplications.css') }}" rel="stylesheet">
        <ul class="step-wizard-list">
            <li class="step-wizard-item">
                <span class="progress-count first-progress-bar">1</span>
                <span class="progress-label">{{ trans('app.Buyurtmachi korxonani qo\'shish') }}</span>
            </li>
            <li class="step-wizard-item current-item">
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

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <form method="post" action="{!! url('sifat-sertificates/store') !!}" enctype="multipart/form-data"
                                class="form-horizontal upperform">
                                <div class="row" style="column-gap: 0;">

                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="organization" value="{{ $organization }}">
                                    @if($user->branch_id == \App\Models\User::BRANCH_STATE)
                                        <div class="col-md-6">
                                            <div class="form-group overflow-hidden">
                                                <label class="form-label">{{ trans('app.Laboratoriyani tanlang') }}<label
                                                        class="text-danger">*</label></label>
                                                <select class="w-100 form-control name_of_corn custom-select" name="laboratory" required>
                                                    @if (count($laboratories))
                                                        <option value="">
                                                            {{ trans('app.Laboratoriyani tanlang') }}
                                                        </option>
                                                    @endif
                                                    @if (!empty($laboratories))
                                                        @foreach ($laboratories as $name)
                                                            <option value="{{ $name->id }}"> {{ $name->name }} </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group {{ $errors->has('dob') ? ' has-error' : '' }}">
                                            <label class="form-label">{{trans('app.Ariza sanasi')}} <label class="text-danger">*</label></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                    </div>
                                                </div>
                                                <input type="text" id="date_of_birth" class="form-control dob" placeholder="<?php echo getDatepicker();?>" name="dob" value="{{ old('dob') }}" onkeypress="return false;" required />
                                            </div>
                                            @if ($errors->has('dob'))
                                                <span class="help-block">
                                                <strong style="margin-left:27%;">Ariza sanasi noto'g'ti shaklda kiritilgan</strong>
                                            </span>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="col-md-6">
                                        <div class="form-group overflow-hidden">
                                            <label class="form-label">{{ trans('app.Sertifikatlanuvchi mahsulot') }}<label
                                                    class="text-danger">*</label></label>
                                            <select class="w-100 form-control name_of_corn custom-select" name="name" required
                                                id="crops_name" url="{!! url('/gettypefromname') !!}">
                                                @if (count($names))
                                                    <option value="">
                                                        {{ trans('app.Sertifikatlanuvchi mahsulot turini tanlang') }}
                                                    </option>
                                                @endif
                                                @if (!empty($names))
                                                    @foreach ($names as $name)
                                                        <option value="{{ $name->id }}"> {{ $name->name }} </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if ($errors->has('name'))
                                                <span class="help-block">
                                                <strong class="hf-warning">{{ $errors->first('name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div id="tin-container" class="col-md-6 legal-fields">
                                        <div class="form-group">
                                            <label class="form-label">{{ trans('app.Kod TN VED') }}<label
                                                    class="text-danger">*</label></label>
                                            <input class="form-control" id="kodtnved" type="text" name="tnved"
                                                data-field-name="tin" data-field-length="10" minlength="10"
                                                data-mask="0000000000" maxlength="10" required="required"
                                                title="10ta raqam kiriting!" data-pattern-mismatch="Noto'g'ri shakl"
                                                value="{{ old('tnved') }}" />
                                            @if ($errors->has('tnved'))
                                                <span class="help-block">
                                                <strong class="hf-warning">{{ $errors->first('tnved') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6 form-group has-feedback {{ $errors->has('party_number') ? ' has-error' : '' }}">
                                        <label for="middle-name"
                                            class="form-label">{{ trans('app.Toʼda (partiya) raqami') }} <label
                                                class="text-danger">*</label></label>
                                        <input type="text" class="form-control" maxlength="25" name="party_number" required
                                            value="{{ old('party_number') }}">
                                        @if ($errors->has('party_number'))
                                            <span class="help-block">
                                                <strong class="hf-warning">{{ $errors->first('party_number') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div
                                        class="col-md-6 form-group has-feedback {{ $errors->has('selection_code') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label ">Seleksion navining kodi<label
                                                class="text-danger">*</label> </label>
                                        <select id="selection_code" class="form-control owner_search" name="selection_code"
                                            required>
                                        </select>
                                        @if ($errors->has('selection_code'))
                                            <span class="help-block">
                                                <strong class="hf-warning">{{ $errors->first('selection_code') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div
                                        class="col-md-12 form-group has-feedback {{ $errors->has('amount') ? ' has-error' : '' }}">
                                        <label for="middle-name" class="form-label">{{ trans('app.amount2') }} <label
                                                class="text-danger">*</label></label>
                                        <input type="number" step="0.01" class="form-control" maxlength="25"
                                            value="{{ old('amount') }}" name="amount" required>
                                        @if ($errors->has('amount'))
                                            <span class="help-block">
                                                <strong class="hf-warning">{{ $errors->first('amount') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <input name="measure_type" type="hidden" value="2">


                                    <div class="form-group col-md-12 col-sm-12">
                                        <div class="col-md-12 col-sm-12 text-center">
                                            <a class="btn btn-primary"
                                                href="{{ URL::previous() }}">{{ trans('app.Cancel') }}</a>
                                            <button type="submit" class="btn btn-success" onclick="disableButton()"
                                                id="submitter">{{ trans('app.Submit') }}</button>
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
    <script src="{{ asset('vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript">
        $("input.dob").datetimepicker({
            format: "dd-mm-yyyy",
            autoclose: 1,
            minView: 2,
            startView: 'decade',
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
        $(document).ready(function() {
            $('select.owner_search').select2({
                ajax: {
                    url: '/crops_selection/search_by_name',
                    delay: 300,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            search: params.term
                        }
                    },
                    processResults: function(data) {
                        data = data.map((name, index) => {
                            return {
                                id: name.id,
                                text: capitalize(name.name + (name.name ? ' - Kod:' + name.kod :
                                    ''))
                            }
                        });
                        return {
                            results: data
                        }
                    }
                },
                language: {
                    inputTooShort: function() {
                        return 'Seleksion navining kodni kiritib izlang';
                    },
                    searching: function() {
                        return 'Izlanmoqda...';
                    },
                    noResults: function() {
                        return "Natija topilmadi"
                    },
                    errorLoading: function() {
                        return "Natija topilmadi"
                    }
                },
                placeholder: 'Seleksion navini kiriting',
                minimumInputLength: 1
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
    <script>
        $(document).ready(function() {
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
                success: function(response) {
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
                success: function(response) {
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

        $('select.name_of_corn').on('change', function() {
            getTypeOfCorn($(this));
        });

        if ($('select.type_of_corn').attr('val')) {
            getTypeOfCorn($('select.name_of_corn'));
        }
        if ($('select.type_of_corn2').attr('val')) {
            getTypeOfCorn($('select.name_of_corn'));
        }
    </script>
@endsection
