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
        @if($company)
            <div class="row massage">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="alert alert-success text-center">
                        <label for="checkbox-10 colo_success"> Ushbu korxona uchun shartnoma ma'lumotlarini kiriting! </label>
                    </div>
                </div>
            </div>
        @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="section">
                            <form method="post" action="{!! url('sifat-contracts/store') !!}" enctype="multipart/form-data"
                                class="form-horizontal upperform">
                                <div class="row" style="column-gap: 0;">

                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="company_id" value="{{ optional($company)->id }}">

                                    <div class="col-md-6">
                                        <div class="form-group" >
                                            <label class="form-label" for="organization">
                                                {{trans('app.Buyurtmachi korxona yoki tashkilot nomi')}} <span class="text-danger">*</span>
                                            </label>
                                            <select id="organization"
                                                    class="form-control @if(!$company) owner_search @endif" name="organization" required>
                                                @if( $company)
                                                    <option selected value="{{ $company->id }}"> {{$company->name}}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-6 form-group has-feedback {{ $errors->has('number') ? ' has-error' : '' }}">
                                        <label for="middle-name"
                                            class="form-label">Shartnoma raqami <label
                                                class="text-danger">*</label></label>
                                        <input type="text" class="form-control" maxlength="15" name="number" required
                                            value="{{ old('number') }}">
                                        @if ($errors->has('number'))
                                            <span class="help-block">
                                                <strong class="hf-warning">{{ $errors->first('number') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label certificate">Shartnoma faylini yuklang</label>
                                        <input class="form-control" type="file" placeholder="Asos hujjatni yuklang..."
                                               required name="reason-file"
                                               accept="application/pdf"
                                        />
                                    </div>
                                    <div class="col-md-6 form-group {{ $errors->has('given_date') ? ' has-error' : '' }}">
                                        <label class="form-label ">Shartnoma berilgan sana <label class="text-danger">*</label></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="given_date" class="form-control given_date" placeholder="<?php echo getDatepicker();?>" name="given_date" value="{{ old('given_date') }}" onkeypress="return false;" required/>
                                        </div>
                                        @if ($errors->has('given_date'))
                                            <span class="help-block">
											<strong style="margin-left:27%;">Sana noto'g'ti shaklda kiritilgan</strong>
										</span>
                                        @endif
                                    </div>

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
        $("input.given_date").datetimepicker({
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

@endsection
