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
                                                                        {{ $data[$i]['id'] }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(isset($data[$i]))
                                                                        <div class="input-container">
                                                                            <input type="text" step="0.1" class="form-control" name="amount{{ 50 * ($loop->iteration - 1) + $i +1 }}" id="amount{{ 50 * ($loop->iteration - 1) + $i +1 }}"
                                                                                 value="{{$data[$i]['amount']}}" @if($data[$i]['amount']) {{'readonly'}} @endif>
                                                                            @if($data[$i]['amount']) <i class="fa fa-pencil pencil" onclick="changeDisplay(this,{{$data[$i]['id']}})"></i> @endif
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
                                <button type="submit" onclick="disableButton()" id="submitter" class="btn btn-success">{{ trans('app.Submit')}}</button>
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

                    reader.onload = function(e) {
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, { type: 'array' });
                        const sheetName = workbook.SheetNames[0]; // Use the first sheet
                        const sheet = workbook.Sheets[sheetName];
                        const jsonData = XLSX.utils.sheet_to_json(sheet);

                        // Call a function to populate inputs with amounts based on "№ toy"
                        populateInputs(jsonData);
                    };

                    reader.readAsArrayBuffer(file);
                });

                function populateInputs(data) {

                    data.forEach((row, rowIndex) => {
                        // Assuming that '№ toy' is the identifier and "Toy og'irligi, kg" is the amount
                        var toyNumber = row['№ toy '] ?? row['№ toy'];
                        var amount = row["Toy og'irligi, kg"] ?? row["Toy og`irligi,kg"];


                        let myRow = Math.floor(Object.keys(row).length / 2);

                        // Find the input field by its ID (assuming toy numbers align with input IDs like 'amount1', 'amount2', etc.)
                        var inputField = document.getElementById('amount' + toyNumber);

                        if (inputField) {
                            if(amount){
                                inputField.value = amount;
                            }
                        }

                        for ( let i = 1; i < 5; i++ ){
                            toyNumber = row['№ toy _' + i] ??  row['№ toy_' + i];
                            amount = row["Toy og'irligi, kg_" + i] ?? row["Toy og`irligi,kg_" + i];
                            inputField = document.getElementById('amount' + toyNumber);
                            if (inputField) {
                                if(amount){
                                    inputField.value = amount;
                                }
                            }
                        }
                    });
                }
            </script>
@endsection
