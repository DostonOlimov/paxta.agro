@extends('layouts.front')
@section('content')

    @if(Auth::user()->zavod_id)
        <link href="{{ asset('assets/css/formApplications.css') }}" rel="stylesheet">
        <ul class="step-wizard-list">
            <li class="step-wizard-item ">
                <span class="progress-count first-progress-bar">1</span>
                <span class="progress-label">{{trans('app.Buyurtmachi korxonani qo\'shish')}}</span>
            </li>
            <li class="step-wizard-item ">
                <span class="progress-count">2</span>
                <span class="progress-label">{{trans('app.Mahsulot ma\'lumotlari')}}</span>
            </li>
            <li class="step-wizard-item current-item">
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

                            <form method="post" action="{!! url('sifat-sertificates/client_store') !!}" enctype="multipart/form-data"  class="form-horizontal upperform">
                                <div class="row" >

                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <input type="hidden" name="id" value="{{$id}}">

                                    <div class="col-md-4 form-group has-feedback {{ $errors->has('client') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label ">Xaridor nomini kiriting<label class="text-danger">*</label> </label>
                                        <select id="client" class="form-control owner_search" name="client" required>
                                            @if(!empty($clients))
                                                @foreach ($clients as $select)
                                                    <option selected  value="{{ $select->id }}">{{$select->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('client'))
                                            <span class="help-block">
                                                <strong>
                                                    Xaridor nomi noto'g'ri shaklda kiritilgan</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 form-group has-feedback {{ $errors->has('number') ? ' has-error' : '' }}">
                                        <label for="middle-name" class="form-label">{{trans('app.Avtotransport/vagon raqami')}} <label class="text-danger">*</label></label>
                                        <input type="text" class="form-control" maxlength="25"  name="number" value="{{ old('number')}}">
                                        @if ($errors->has('number'))
                                            <span class="help-block">
											 <strong>Avtotransport/vagon raqami noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 form-group has-feedback {{ $errors->has('yuk_xati') ? ' has-error' : '' }}">
                                        <label for="middle-name" class="form-label">{{trans('app.Yuk xati raqami')}} <label class="text-danger">*</label></label>
                                        <input type="text" class="form-control" maxlength="25"  name="yuk_xati" value="{{ old('yuk_xati')}}">
                                        @if ($errors->has('yuk_xati'))
                                            <span class="help-block">
											 <strong>Yuk xati raqami noto'g'ri shaklda kiritilgan</strong>
										   </span>
                                        @endif
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
                    url: '/clients/search_by_name',
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
                                text: capitalize(name.name + (name.name ? ' - Kod:' + name.kod : ''))
                            }
                        });
                        return {
                            results: data
                        }
                    }
                },
                language: {
                    inputTooShort: function () {
                        return 'Xaridor nomini kiritib izlang..';
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

@endsection