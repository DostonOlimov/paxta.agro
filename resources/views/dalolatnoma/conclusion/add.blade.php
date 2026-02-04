@extends('layouts.app')
@section('styles')
    <style>
        .form-group {
            margin-bottom: 0 !important;
        }

        /* Style for the container holding the file input */
        .file-input-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
            border: 2px solid #ccc;
            background-color: #21c44c;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Style for the actual file input */
        .file-input {
            position: absolute;
            font-size: 100px;
            opacity: 0;
            right: 0;
            top: 0;
        }

        /* Style for the text label */
        .file-label {
            font-size: 14px;
            pointer-events: none;
        }

        .twoButtonsContainer {
            display: flex !important;
            align-items: center;
            height: 110px;
        }

        .twoButtonsContainer #addButton,
        .twoButtonsContainer .col-md-4 {
            display: flex;
            align-items: center;
        }

        .file-input-container {
            cursor: pointer !important;
            margin-top: 0px !important;
        }

        .form-control,
        .form-group {
            width: 100%;
        }
    </style>
@endsection
@section('content')
    <!-- page content -->
    <?php $userid = Auth::user()->id; ?>
    @can('create', \App\Models\Application::class)
        <div class="section">
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp Na'muna olish dalolatnomasini qo'shish
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
                                            <i class="fa fa-plus-circle fa-lg">&nbsp;</i>
                                            <b>{{ trans('app.Qo\'shish') }}</b>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <form id="invoice-form" method="post" action="{!! url('dalolatnoma/store') !!}"
                                enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="test_id" value="{{ $test->id }}">

                                    <div
                                        class="col-md-6 form-group has-feedback {{ $errors->has('number') ? ' has-error' : '' }}">
                                        <label for="number" class="form-label certificate">Dalolatnoma raqami <label
                                                class="text-danger">*</label></label>
                                        <input type="number" class="form-control" value="{{ old('number') }}" name="number"
                                            required id="number">
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
                                                placeholder="<?php echo getDatepicker(); ?>" name="date" value="{{ old('date') }}"
                                                onkeypress="return false;" required />
                                        </div>
                                        @if ($errors->has('date'))
                                            <span class="help-block">
                                                <strong style="margin-left:27%;">Sana noto'g'ti shaklda kiritilgan</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="row">
                                        {{-- select end --}}
                                        <div
                                            class="col-md-6 form-group has-feedback {{ $errors->has('toy_count') ? ' has-error' : '' }}">
                                            <label for="number" class="form-label ">Jami na'munalar soni<label
                                                    class="text-danger">*</label> </label>
                                            <input type="number" class="form-control" value="{{ old('toy_count') }}"
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
                                                value="{{ old('amount') }}" name="amount" required>
                                            @if ($errors->has('amount'))
                                                <span class="help-block">
                                                    <strong>
                                                        Sinf no'g'ri shaklda kiritilgan</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- start --}}
                                    {{-- <div class="certificate row" id="forms">
                                        <div class="col-md-4 row">
                                            <label for="number" class="form-label">Shtrix kod raqami:<label
                                                    class="text-danger">*</label></label>
                                            <div class="col-md-6 form-group has-feedback">
                                                <input type="number" class="form-control" maxlength="10"
                                                    value="@if (old('kod_toy')) {{ old('kod_toy')[0][0] }} @endif"
                                                    name="kod_toy[0][0]" required min="1">
                                                <label for="number" class="form-label">dan</label>
                                            </div>

                                            <div class="col-md-6 form-group has-feedback">
                                                <input type="number" class="form-control"
                                                    value="@if (old('kod_toy')) {{ old('kod_toy')[0][1] }} @endif"
                                                    name="kod_toy[0][1]" required>
                                                <label for="number" class="form-label">gacha </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 row">
                                            <label for="number" class="form-label">Toylar ketma-ketligi:<label
                                                    class="text-danger">*</label></label>
                                            <div
                                                class="col-md-6 form-group has-feedback {{ $errors->has('kod_toy[0][2]') ? ' has-error' : '' }}">
                                                <input type="number" class="form-control" maxlength="10"
                                                    value="@if (old('kod_toy')) {{ old('kod_toy')[0][2] }} @endif"
                                                    name="kod_toy[0][2]" required min="1">
                                                <label for="number" class="form-label">dan</label>
                                            </div>
                                            <div class="col-md-6 form-group has-feedback ">
                                                <input type="number" class="form-control"
                                                    value="@if (old('kod_toy')) {{ old('kod_toy')[0][3] }} @endif"
                                                    name="kod_toy[0][3]" required>
                                                <label for="number" class="form-label">gacha </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 row twoButtonsContainer">
                                            <div class="col-md-3">
                                                <div id="addButton" onclick="addField();" class="btn btn-success"> <i
                                                        class="fa fa-plus-circle fa-lg">&nbsp;</i>
                                                    <b>{{ trans('app.Qo\'shish') }}</b>
                                                </div>
                                            </div>
                                        </div>
                                        @if (old('kod_toy'))
                                            @for ($i = 1; $i < count(old('kod_toy')); $i++)
                                                <div class="col-md-4 row">
                                                    <div class="col-md-4 form-group has-feedback">
                                                        <input type="number" class="form-control" maxlength="10"
                                                            value="{{ old('kod_toy')[$i][0] }}"
                                                            name="kod_toy[{{ $i }}][0]" required min="1">
                                                    </div>
                                                    <div class="col-md-4 form-group has-feedback">
                                                        <input type="number" class="form-control"
                                                            value="{{ old('kod_toy')[$i][1] }}"
                                                            name="kod_toy[{{ $i }}][1]" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 row">
                                                    <div class="col-md-4 form-group has-feedback">
                                                        <input type="number" class="form-control" maxlength="10"
                                                            value="{{ old('kod_toy')[$i][2] }}"
                                                            name="kod_toy[{{ $i }}][2]" required min="1">
                                                    </div>
                                                    <div class="col-md-4 form-group has-feedback ">
                                                        <input type="number" class="form-control"
                                                            value="{{ old('kod_toy')[$i][3] }}"
                                                            name="kod_toy[{{ $i }}][3]" required>
                                                    </div>
                                                </div>
                                            @endfor
                                        @endif
                                    </div> --}}
                                    <div id="numbersContainer" class="row"></div>
                                    <div class="form-group col-md-12 col-sm-12 mt-2">
                                        <div class="col-md-4 col-sm-6">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <script src="{{ URL::asset('vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{ URL::asset('vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ URL::asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript">
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
    <script>
        document.getElementById('excelFile').addEventListener('change', function(event) {
            const file = event.target.files[0];

            const reader = new FileReader();
            reader.onload = function(event) {
                const data = new Uint8Array(event.target.result);
                const workbook = XLSX.read(data, {
                    type: 'array'
                });
                const sheetName = workbook.SheetNames[0]; // Assuming we are reading the first sheet
                const sheet = workbook.Sheets[sheetName];

                const numbers = [];
                const columnIndices = ['A', 'B', 'D',
                'E']; // Assuming you want to read from columns A, B, C, and D

                // Iterate over each row and each column to read the values
                let rowIndex = 1; // Start from the first row
                let cellAddress = '';
                columnIndices.forEach(columnIndex => {
                    cellAddress = columnIndex + rowIndex;
                    let cell = sheet[cellAddress];
                    while (cell !== undefined) {
                        const cellValue = cell.v;
                        if (!numbers[rowIndex - 1]) {
                            numbers[rowIndex - 1] = [];
                        }
                        numbers[rowIndex - 1].push(cellValue);
                        rowIndex++;
                        cellAddress = columnIndex + rowIndex;
                        cell = sheet[cellAddress];
                    }
                    rowIndex = 1; // Reset rowIndex for the next column
                });

                // Display numbers in UI
                const inputFieldsContainer = document.getElementById('numbersContainer');
                const addButton = document.getElementById('addButton');
                addButton.style.display = 'none';
                inputFieldsContainer.innerHTML = '';
                numbers.forEach((row, index) => {
                    if (index === 0) {
                        const inputField = document.querySelector(
                            `input[name="kod_toy[${index}][${0}]"]`);
                        const inputField1 = document.querySelector(
                            `input[name="kod_toy[${index}][${1}]"]`);
                        const inputField2 = document.querySelector(
                            `input[name="kod_toy[${index}][${2}]"]`);
                        const inputField3 = document.querySelector(
                            `input[name="kod_toy[${index}][${3}]"]`);
                        if (inputField) {
                            inputField.value = row[0];
                            inputField1.value = row[1];
                            inputField2.value = row[2];
                            inputField3.value = row[3];
                        }
                    } else {
                        inputFieldsContainer.innerHTML += `
            <div class="col-md-4 row">
                <div class="col-md-6 form-group has-feedback">
                    <input type="number" class="form-control" maxlength="10"
                        value="${row[0]}" name="kod_toy[${index}][0]" required min="1">
                </div>
                <div class="col-md-6 form-group has-feedback">
                    <input type="number" class="form-control" maxlength="10"
                        value="${row[1]}" name="kod_toy[${index}][1]" required min="1">
                </div>
            </div>
            <div class="col-md-4 row">
                <div class="col-md-6 form-group has-feedback">
                    <input type="number" class="form-control" maxlength="10"
                        value="${row[2]}" name="kod_toy[${index}][2]" required min="1">
                </div>
                <div class="col-md-6 form-group has-feedback">
                    <input type="number" class="form-control" maxlength="10"
                        value="${row[3]}" name="kod_toy[${index}][3]" required min="1">
                </div>
            </div>
            <div class="col-md-4 row">
            </div>
            `;
                    }
                });
            };
            reader.readAsArrayBuffer(file);
        });
    </script>
    <script>
        var fieldId = 0;

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
                `<br>  <div class="row">  <div class="col-md-4 row">
                                            <div
                                                class="col-md-4 form-group has-feedback {{ $errors->has('kod_toy[`+ fieldId + `][0]') ? ' has-error' : '' }}">
                                                <input type="number" class="form-control" maxlength="10"
                                                    value="{{ old('kod_toy[`+ fieldId + `][0]') }}" name="kod_toy[` +
                fieldId + `][]" required>
                                                @if ($errors->has('kod_toy[`+ fieldId + `][0]'))
                <span class="help-block">
                    <strong
                        class="text-danger">{{ $errors->first('kod_toy[`+ fieldId + `][0]') }}</strong>
                                                    </span>
                                                @endif
                </div>
                <div
                    class="col-md-4 form-group has-feedback {{ $errors->has('kod_toy[`+ fieldId + `][1]') ? ' has-error' : '' }}">
                                                <input type="number" class="form-control" value="{{ old('kod_toy[`+ fieldId + `][1]') }}"
                                                    name="kod_toy[` + fieldId + `][]" required>
                                                @if ($errors->has('kod_toy[`+ fieldId + `][1]'))
                <span class="help-block">
                    <strong
                        class="text-danger">{{ $errors->first('kod_toy[`+ fieldId + `][1]') }}</strong>
                                                    </span>
                                                @endif
                </div>
            </div>
            <div class="col-md-4 row">
                <div
                    class="col-md-4 form-group has-feedback {{ $errors->has('kod_toy[`+ fieldId + `][2]') ? ' has-error' : '' }}">
                                                <input type="number" class="form-control" maxlength="10"
                                                    value="{{ old('kod_toy[`+ fieldId + `][2]') }}" name="kod_toy[` +
                fieldId + `][]" required>
                                            </div>
                                            <div
                                                class="col-md-4 form-group has-feedback {{ $errors->has('kod_toy[`+ fieldId + `][3]') ? ' has-error' : '' }}">
                                                <input type="number" class="form-control" value="{{ old('kod_toy[`+ fieldId + `][3]') }}"
                                                    name="kod_toy[` + fieldId + `][]" required>
                                                @if ($errors->has('kod_toy[`+ fieldId + `][3]'))
                <span class="help-block">
                    <strong
                        class="text-danger">{{ $errors->first('kod_toy[`+ fieldId + `][3]') }}</strong>
                                                    </span>
                                                @endif
                </div>
            </div>` +
                `
                                                <div class="col-md-2 row "> <label></label>
                                                     <div class="col-md-4 col-sm-6"> <div onclick="removeField(` +
                fieldId + `);" class="btn btn-danger">
                                                         <i class="fa fa-minus-circle fa-lg">&nbsp;</i>
                                                    <b>{{ trans('app.Olib tashlash') }}</b></div>
                                            </div>
                                        </div>
                                         </div>`;
            addElement('forms', 'div', 'field-' + fieldId, html);
        }
    </script>
@endsection
