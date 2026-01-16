@extends('layouts.app')
@section('styles')
    <style>
        .form-group {
            margin-bottom: 0 !important;
        }
    </style>
@endsection
@section('content')
    <!-- page content -->
    @can('create', \App\Models\Application::class)
        <div class="section">
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp Dalolatnoma ma'lumotlarini o'zgartirish
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
                                            <a href="{!! url('/dalolatnoma/search') !!}">
                                                <span class="visible-xs"></span>
                                                <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Ro\'yxat') }}
                                            </a>
                                        </li>
                                        <li class="active">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-pencil fa-lg">&nbsp;</i>
                                            <b>{{ trans('O\'zgartirish') }}</b>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <form id="invoice-form" method="post" action="update2/{{ $result->id }}"
                                enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">
                                @csrf
                                <div class="row">
                                    <div
                                        class="col-md-6 form-group has-feedback {{ $errors->has('number') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label certificate">Dalolatnoma raqami <label
                                                class="text-danger">*</label></label>
                                        <input type="number" class="form-control" value="{{ $result->number }}" name="number"
                                            required>
                                        @if ($errors->has('number'))
                                            <span class="help-block">
                                                <strong>Dalolatnoma raqami noto'g'ri shaklda kiritilgan</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 form-group {{ $errors->has('date') ? ' has-error' : '' }}">
                                        <label class="form-label certificate">Dalolatnoma sanasi <label
                                                class="text-danger">*</label></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                </div>
                                            </div>
                                            <input type="text" id="date" class="form-control date"
                                                placeholder="<?php echo getDatepicker(); ?>" name="date" value="{{ $result->date }}"
                                                onkeypress="return false;" required />
                                        </div>
                                        @if ($errors->has('date'))
                                            <span class="help-block">
                                                <strong style="margin-left:27%;">Sana noto'g'ti shaklda kiritilgan</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="row">
                                        
                                        <div
                                            class="col-md-6 form-group has-feedback {{ $errors->has('toy_count') ? ' has-error' : '' }}">
                                            <label for="number" class="form-label ">Jami na'munalar soni<label
                                                    class="text-danger">*</label> </label>
                                            <input type="number" class="form-control" value="{{ $result->toy_count }}"
                                                name="toy_count" required>
                                            @if ($errors->has('toy_count'))
                                                <span class="help-block">
                                                    <strong class="text-danger">{{ $errors->first('toy_count') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div
                                            class="col-md-6 form-group has-feedback {{ $errors->has('amount') ? ' has-error' : '' }}">
                                            <label for="number" class="form-label ">Olingan na'munaning og'irligi(kg)<label
                                                    class="text-danger">*</label></label>
                                            <input type="number" step="0.01" class="form-control"
                                                value="{{ $result->amount }}" name="amount" required>
                                            @if ($errors->has('amount'))
                                                <span class="help-block">
                                                    <strong>
                                                        Sinf no'g'ri shaklda kiritilgan</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                   

                                    <div class="certificate row" id="forms" style="margin-bottom: 1%">

                                        @foreach ($gin_balles as $key => $item)
                                            <input type="text" hidden name="kod_toy[{{ $key }}][0]"
                                                value="{{ optional($item)->id }}">
                                            <div class="col-md-5 row">
                                                @if ($key == 0)
                                                    <label for="number" class="form-label ">Shtrix kod raqami:<label
                                                            class="text-danger">*</label></label>
                                                @endif
                                                <div
                                                    class="col-md-6 form-group has-feedback {{ $errors->has('kod_toy[' . $key . '][1]') ? ' has-error' : '' }}">
                                                    <input type="number" class="form-control" maxlength="10"
                                                        value="{{ optional($item)->from_number }}"
                                                        name="kod_toy[{{ $key }}][1]" required>
                                                    @if ($key == 0)
                                                        <label for="number" class="form-label ">dan</label>
                                                    @endif
                                                    @if ($errors->has('kod_toy[' . $key . '}}][1]'))
                                                        <span class="help-block">
                                                            <strong
                                                                class="text-danger">{{ $errors->first('kod_toy[' . $key . '][1]') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div
                                                    class="col-md-6 form-group has-feedback {{ $errors->has('kod_toy[' . $key . '][2]') ? ' has-error' : '' }}">
                                                    <input type="number" class="form-control"
                                                        value="{{ optional($item)->to_number }}"
                                                        name="kod_toy[{{ $key }}][2]" required>
                                                    @if ($key == 0)
                                                        <label for="number" class="form-label ">gacha </label>
                                                    @endif
                                                    @if ($errors->has('kod_toy[{{ $key }}][2]'))
                                                        <span class="help-block">
                                                            <strong
                                                                class="text-danger">{{ $errors->first('kod_toy[' . $key . '][2]') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-5 row">
                                                @if ($key == 0)
                                                    <label for="number" class="form-label ">Toylar ketma-ketligi:<label
                                                            class="text-danger">*</label></label>
                                                @endif
                                                <div
                                                    class="col-md-6 form-group has-feedback {{ $errors->has('kod_toy[' . $key . '][3]') ? ' has-error' : '' }}">
                                                    <input type="number" style="margin:5px" class="form-control" maxlength="10"
                                                        value="{{ optional($item)->from_toy }}"
                                                        name="kod_toy[{{ $key }}][3]" required>
                                                    @if ($key == 0)
                                                        <label for="number" class="form-label ">dan</label>
                                                    @endif
                                                </div>
                                                <div
                                                    class="col-md-6 form-group has-feedback {{ $errors->has('kod_toy[' . $key . '][4]') ? ' has-error' : '' }}">
                                                    <input type="number" class="form-control"
                                                        value="{{ optional($item)->to_toy }}"
                                                        name="kod_toy[{{ $key }}][4]" required>
                                                    @if ($key == 0)
                                                        <label for="number" class="form-label ">gacha </label>
                                                    @endif
                                                </div>
                                            </div>


                                            {{-- start --}}
                                            @if ($key == 0)
                                                <div class="col-md-2 row ">
                                                    <label></label>
                                                    <div class="col-md-6 col-sm-6">
                                                        <div onclick="addField();" class="btn btn-success"> <i
                                                                class="fa fa-plus-circle fa-lg">&nbsp;</i>
                                                            <b>{{ trans('app.Qo\'shish') }}</b>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                        <script>
                                            var fieldId = @json($gin_balles->count() - 1); ///

                                            function addElement(parentId, elementTag, elementId, html) {
                                                var id = document.getElementById(parentId);
                                                var newElement = document.createElement(elementTag);
                                                newElement.setAttribute('id', elementId);
                                                newElement.innerHTML = html;
                                                id.appendChild(newElement);
                                            }

                                            function removeField(elementId) {
                                                var fieldId = "field-" + elementId;
                                                var element = document.getElementById(fieldId);
                                                element.parentNode.removeChild(element);
                                            }

                                            function addField() {
                                                fieldId++;
                                                var html =
                                                    `<br>
                                                    <input type="text"  hidden name="kod_toy[` + fieldId +
                                                    `][0]" value="null">
                                                     <div class="row">  <div class="col-md-5 row">
                                                            <div class="col-md-6 form-group has-feedback">
                                                                <input type="number" class="form-control" maxlength="10"
                                                                    value="{{ old('kod_toy[`+ fieldId + `][1]') }}" name="kod_toy[` +
                                                    fieldId + `][1]" required>
                                                                @if ($errors->has('kod_toy[`+ fieldId + `][1]'))
                                                                    <span class="help-block">
                                                                        <strong class="text-danger">{{ $errors->first('kod_toy[`+ fieldId + `][1]') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-6 form-group has-feedback">
                                                                <input type="number" class="form-control" value="{{ old('kod_toy[`+ fieldId + `][2]') }}"
                                                                    name="kod_toy[` + fieldId +
                                                    `][2]" required>
                                                                @if ($errors->has('kod_toy[`+ fieldId + `][2]'))
                                                                    <span class="help-block">
                                                                        <strong class="text-danger">{{ $errors->first('kod_toy[`+ fieldId + `][2]') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5 row">
                                                            <div class="col-md-6 form-group has-feedback">
                                                                <input type="number" class="form-control" maxlength="10"
                                                                    value="{{ old('kod_toy[`+ fieldId + `][3]') }}" name="kod_toy[` +
                                                    fieldId + `][3]" required>
                                                            </div>
                                                            <div class="col-md-6 form-group has-feedback">
                                                                <input type="number" class="form-control" value="{{ old('kod_toy[`+ fieldId + `][4]') }}"
                                                                    name="kod_toy[` + fieldId + `][4]" required>
                                                                @if ($errors->has('kod_toy[`+ fieldId + `][4]'))
                                                                    <span class="help-block">
                                                                        <strong class="text-danger">{{ $errors->first('kod_toy[`+ fieldId + `][4]') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>` +
                                                    `<div class="col-md-2 row "> <label></label>
                                                            <div class="col-md-6 col-sm-6"> <div onclick="removeField(` +
                                                    fieldId + `);" class="btn btn-danger">
                                                                    <i class="fa fa-minus-circle fa-lg">&nbsp;</i>
                                                                    {{ trans('app.Olib tashlash') }}</div>
                                                            </div>
                                                        </div>
                                                    </div> <br>`;
                                                addElement('forms', 'div', 'field-' + fieldId, html);
                                            }
                                        </script>

                                        {{-- end --}}
                                    </div>

                                    <div class="form-group col-md-12 col-sm-12">
                                        <div class="col-md-6 col-sm-6">
                                            <a class="btn btn-primary"
                                                href="{{ URL::previous() }}">{{ trans('app.Cancel') }}</a>
                                            <button type="submit" onclick="disableButton()" id="submitter"
                                                class="btn btn-success">{{ trans('app.Submit') }}</button>
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
                    <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp
                        {{ trans('app.You Are Not Authorize This page.') }}</span>
                </div>
            </div>
        </div>
    @endcan
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="{{ URL::asset('vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{ URL::asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ URL::asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $("input.date").datetimepicker({
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
            }, 5000);
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
@endsection
