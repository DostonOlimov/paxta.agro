@extends('layouts.app')
@section('content')
    <style>
        .checkbox-success {
            background-color: #cad0cc !important;
            color: red;
        }
    </style>
    <?php $userid = Auth::user()->id; ?>
    @if (getAccessStatusUser('Vehicles',$userid)=='yes')
        @if(getActiveCustomer($userid)=='yes' || getActiveEmployee($userid)=='yes')

            <div class="section">
                <div class="page-header">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <i class="fe fe-life-buoy mr-1"></i>&nbsp Korxona nomini qo'shish
                        </li>
                    </ol>
                </div>
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
                                <div class="panel panel-primary">
                                    <div class="tab_wrapper page-tab">
                                        <ul class="tab_list">
                                            <li>
                                                <a href="{!! url('/crops/list')!!}">
                                                    <span class="visible-xs"></span>
                                                    <i class="fa fa-arrow-left fa-lg">&nbsp;</i>
                                                    {{ trans('Orqaga')}}
                                                </a>
                                            </li>
                                            <li class="active">
                                                <a href="{!! url('/crops/add')!!}">
                                                    <span class="visible-xs"></span>
                                                    <i class="fa fa-plus-circle fa-lg">&nbsp;</i> <b>
                                                        {{ trans('app.Qo\'shish')}}</b>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <form action="{{ url('/crops/store') }}" method="post"
                                              enctype="multipart/form-data" data-parsley-validate
                                              class="form-horizontal form-label-left">
                                    <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group overflow-hidden">
                                                        <label class="form-label">Urug'lik turi<label
                                                                class="text-danger">*</label></label>
                                                        <select class="w-100 form-control state_of_country custom-select" name="name"
                                                                url="{!! url('/gettypefromname') !!}">
                                                            @if(count($names))
                                                                <option value="">Urug'lik turini tanlang</option>
                                                            @endif
                                                            @if(!empty($names))
                                                                @foreach($names as $name)
                                                                    <option value="{{ $name->id }}"> {{$name->name}} </option>
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
                                                       title="10ta raqam kiriting!" data-pattern-mismatch="Noto'g'ri shakl"
                                                />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group overflow-hidden">
                                                <label class="form-label">Ishlab chiqarilgan mamlakat<label
                                                        class="text-danger">*</label></label>
                                                <select class="w-100 form-control" name="country">
                                                    @if(count($countries))
                                                        <option value="">Mamlakat nomini tanlang</option>
                                                    @endif
                                                    @if(!empty($countries))
                                                        @foreach($countries as $name)
                                                            <option value="{{ $name->id }}"> {{$name->name}} </option>
                                                        @endforeach

                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 form-group has-feedback {{ $errors->has('party_number') ? ' has-error' : '' }}">
                                            <label for="middle-name" class="form-label">Partiya raqami <label class="text-danger">*</label></label>
                                                <input type="text" class="form-control" maxlength="25"  name="party_number">
                                                @if ($errors->has('party_number'))
                                                    <span class="help-block">
											 <strong>Partiya raqami noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                                @endif
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group overflow-hidden">
                                                <label class="form-label">O'lchov turi <label class="text-danger">*</label></label>
                                                <select class="w-100 form-control" name="measure_type">
                                                    @if(count($measure_types))
                                                        <option value="">O'lchov turini tanlang</option>
                                                    @endif
                                                        @foreach($measure_types as $key=>$name)
                                                            <option value="{{ $key }}"> {{$name}} </option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 form-group has-feedback {{ $errors->has('amount') ? ' has-error' : '' }}">
                                            <label for="middle-name" class="form-label">Urug'lik miqdori <label class="text-danger">*</label></label>
                                            <input type="number" step="0.01" class="form-control" maxlength="25"  name="amount">
                                            @if ($errors->has('amount'))
                                                <span class="help-block">
											 <strong>Urug'lik miqdori noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group overflow-hidden">
                                                <label class="form-label">Hosil yili<label class="text-danger">*</label></label>
                                                <select class="w-100 form-control" name="year">
                                                    @if(count($year))
                                                        <option value="">Hosil yilini tanlang</option>
                                                    @endif
                                                    @foreach($year as $key=>$name)
                                                        <option value="{{ $key }}"> {{$name}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group has-feedback">
                                            <label class="form-label" Ishlab chiqarish turi</label>
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

                                                <input type="hidden" name="_token" value="{{csrf_token()}}">
                                                <div class="col-12 col-md-6">
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
