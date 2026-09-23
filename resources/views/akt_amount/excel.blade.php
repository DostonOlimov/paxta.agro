@extends('layouts.app')

@section('content')
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
        .twoButtonsContainer #addButton, .twoButtonsContainer .col-md-4 {
            display: flex;
            align-items: center;
        }
        .file-input-container {
            cursor: pointer !important;
            margin-top: 0px !important;
        }

        .form-control, .form-group {
            width: 100%;
        }
    </style>
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp Og'irlik bo'yicha dalolatnoma
                </li>
            </ol>
        </div>
        @if(session('message'))
            <div class="row massage">
                <div class="col-md-12 col-sm-12">
                    <div class="alert alert-success text-center">
                        <input id="checkbox-10" type="checkbox" checked="">
                        <label for="checkbox-10 colo_success">  {{session('message')}} </label>
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
                                        <a href="{!! url('/akt_amount/search')!!}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Ro\'yxat')}}
                                        </a>
                                    </li>
                                    <li class="active">
                                        <span class="visible-xs"></span>
                                        <i class="fa fa-edit fa-lg">&nbsp;</i>
                                        <b>{{ trans('app.Edit')}}</b>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3 file-input-container">
                            <span class="file-label"><i class="fa fa-file-excel-o"></i> Faylni tanlang</span>
                            <input type="file" class="file-input" id="excelFile" name="excelFile" accept=".xlsx, .xls">
                        </div>
                        <div class="col-md-3 file-input-container">
                            <span class="fa fa-upload"></span>
                            <a class="text-white" href="/img/example.xlsx">Na'muna fayli</a>
                        </div>
                        <form id="myForm" method="post" enctype="multipart/form-data"  action="{!! url('akt_amount/store') !!}"
                              data-parsley-validate class="form-horizontal form-label-left">
                            @csrf
                            <input type="hidden" name="id" value="{{$id}}">
                            {{-- All amounts are sent as one JSON field so large acts don't hit PHP max_input_vars --}}
                            <input type="hidden" name="amounts_json" id="amountsJson">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive row">

                                            <table id="examples1" class="table table-striped table-bordered nowrap" style="margin-top:20px;" >
                                                <thead>
                                                </thead>
                                                <tbody>
                                                    @for($i = 0; $i < 50; $i++)
                                                        <tr>
                                                            @foreach($data1 as $data)
                                                                <td>
                                                                    @if(isset($data[$i]))
                                                                        {{ $data[$i]['created_at'] }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(isset($data[$i]))
                                                                        <div class="input-container">
                                                                            <input type="text" class="form-control amount-input"
                                                                                   data-id="{{ $data[$i]['id'] }}"
                                                                                   data-toy="{{ $data[$i]['created_at'] ?? '' }}"
                                                                                   data-position="{{ 50 * ($loop->iteration - 1) + $i + 1 }}"
                                                                                   value="{{$data[$i]['amount']}}" @if($data[$i]['amount']) {{'readonly'}} @endif>
                                                                            @if($data[$i]['amount']) <i class="fa fa-pencil pencil" onclick="changeDisplay(this)"></i> @endif
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="col-md-6 col-sm-6">
                                <a class="btn btn-primary" href="{{ URL::previous() }}">{{ trans('app.Cancel')}}</a>
                                <button type="submit" id="submitter" class="btn btn-success">{{ trans('app.Submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
            <script>
                document.getElementById('excelFile').addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    const reader = new FileReader();

                    if (!file) return;
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, { type: 'array' });
                        const sheet = workbook.Sheets[workbook.SheetNames[0]]; // Use the first sheet
                        const rows = XLSX.utils.sheet_to_json(sheet, { header: 1, defval: null, blankrows: false });

                        populateInputs(rows);
                    };

                    reader.readAsArrayBuffer(file);
                });

                function normalizeAmount(value) {
                    if (value === null || value === undefined) return '';
                    return String(value).trim().replace(/\s/g, '').replace(',', '.');
                }

                // Reads every "№ toy" / "Toy og'irligi" column pair, whatever the number of pairs
                // or small spelling differences in the headers.
                function readPairs(rows) {
                    const isToyHeader = v => typeof v === 'string' && /toy/i.test(v) && !/irlig/i.test(v);
                    const isAmountHeader = v => typeof v === 'string' && /irlig/i.test(v);

                    const headerIndex = rows.findIndex(r => r.some(isToyHeader));
                    if (headerIndex === -1) return [];

                    const header = rows[headerIndex];
                    const columns = [];
                    header.forEach((cell, c) => {
                        if (isToyHeader(cell)) {
                            let a = c + 1;
                            while (a < header.length && !isAmountHeader(header[a]) && !isToyHeader(header[a])) a++;
                            if (isAmountHeader(header[a])) columns.push([c, a]);
                        }
                    });

                    const pairs = [];
                    columns.forEach(([toyCol, amountCol]) => {
                        rows.slice(headerIndex + 1).forEach(r => {
                            const toy = parseInt(r[toyCol], 10);
                            const amount = normalizeAmount(r[amountCol]);
                            if (!isNaN(toy) && amount !== '' && !isNaN(Number(amount))) {
                                pairs.push({ toy, amount });
                            }
                        });
                    });
                    return pairs;
                }

                function populateInputs(rows) {
                    const pairs = readPairs(rows);
                    const inputs = Array.from(document.querySelectorAll('.amount-input'));
                    const byToy = {}, byPosition = {};
                    inputs.forEach(inp => {
                        byToy[inp.dataset.toy] = inp;
                        byPosition[inp.dataset.position] = inp;
                    });

                    // Match by the toy number shown in the table; if the file uses 1..N numbering
                    // instead, fall back to the row position.
                    const toyMatches = pairs.filter(p => byToy[p.toy]).length;
                    const lookup = toyMatches > 0 ? byToy : byPosition;

                    let filled = 0;
                    pairs.forEach(p => {
                        const input = lookup[p.toy];
                        if (input) {
                            input.value = p.amount;
                            filled++;
                        }
                    });

                    alert(filled + ' ta toy og\'irligi fayldan yuklandi' + (pairs.length > filled ? ' (' + (pairs.length - filled) + ' tasi mos kelmadi)' : ''));
                }

                function changeDisplay(elm) {
                    const input = elm.parentNode.querySelector('.amount-input');
                    input.removeAttribute('readonly');
                    input.focus();
                    elm.style.display = 'none';
                }

                document.getElementById('myForm').addEventListener('submit', function () {
                    const amounts = {};
                    document.querySelectorAll('.amount-input').forEach(inp => {
                        const value = normalizeAmount(inp.value);
                        if (value !== '') amounts[inp.dataset.id] = value;
                    });
                    document.getElementById('amountsJson').value = JSON.stringify(amounts);

                    const button = document.getElementById('submitter');
                    button.disabled = true;
                    button.innerText = 'Yuklanmoqda...';
                });
            </script>
@endsection
