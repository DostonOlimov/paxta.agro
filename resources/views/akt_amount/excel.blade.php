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
{{--                                    <li class="btn-success">--}}
{{--                                        <a href="{!! url('/akt_amount/excel/'.$id)!!}">--}}
{{--                                            <span ></span>--}}
{{--                                            <i class="fa fa-file-excel-o  text-white">&nbsp; Excel orqali yuklash</i>--}}
{{--                                        </a>--}}
{{--                                    </li>--}}
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3 file-input-container">
                            <span class="file-label"><i class="fa fa-file-excel-o"></i> Fayl yuklash</span>
                            <input type="file" class="file-input" id="excelFile" name="excelFile" accept=".xlsx, .xls">
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive row">
                                            <table id="examples1" class="table table-striped table-bordered nowrap" style="margin-top:20px;" >
                                                <thead>
                                                <tr>
                                                    @foreach($data1 as $data)
                                                        <th class="border-bottom-0 border-top-0">#</th>
                                                        <th>Og'irlik (kg)</th>
                                                    @endforeach
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <form id="myForm" method="get" enctype="multipart/form-data"
                                                      data-parsley-validate class="form-horizontal form-label-left">
                                                    @csrf
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
                                                                            <input type="text" step="0.1" class="form-control" name="amount" id="amount{{ 50 * ($loop->iteration - 1) + $i +1 }}"  oninput="formatNumber({{$data[$i]['id']}})"  myattr = {{$loop->iteration}}
                                                                                onchange="saveAnswer({{$data[$i]['id']}} , this , {{$loop->iteration}} ) "  value="{{$data[$i]['amount']}}" @if($data[$i]['amount']) {{'disabled'}} @endif>
                                                                            @if($data[$i]['amount']) <i class="fa fa-pencil pencil" onclick="changeDisplay(this,{{$data[$i]['id']}})"></i> @endif
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endfor
                                                </form>
                                                @foreach($data1 as $d)
                                                    <td></td>
                                                    <td><div class="input-container">
                                                            <input type="number" step="0.001" name="sum"  class="form-control" id="sum{{$loop->iteration}}" disabled value="{{array_sum(array_column($d, 'amount'))}}">
                                                            <i class="pencil ">kg</i> </div></td>
                                                @endforeach
                                                </tbody>
                                            </table>
                                            <div id="inputContainer"></div> <!-- Container to dynamically append inputs -->
                                            <div>
                                                <a href="{!! url('/akt_amount/view/'.$id) !!}"><button type="button" class="btn btn-round btn-success">{{ trans('app.Submit')}}</button></a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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

                        // Call a function to dynamically create inputs
                        populateInputs(jsonData);
                    };

                    reader.readAsArrayBuffer(file);
                });

                function populateInputs(data) {
                    const inputContainer = document.getElementById('inputContainer');
                    inputContainer.innerHTML = ''; // Clear the container before adding inputs

                    data.forEach((row, rowIndex) => {
                        // Iterate over each key-value pair in the row
                        Object.entries(row).forEach(([key, value], index) => {
                            // Create a unique name for the input field based on the key
                            const inputName = key.toLowerCase().replace(/[^a-z0-9]/g, ''); // e.g., "Toy og'irligi, kg" becomes "toyogirligikg"

                            // Create a new input element
                            const inputField = document.createElement('input');
                            inputField.type = 'text';
                            inputField.name = inputName + (rowIndex + 1); // Ensure unique name per row (e.g., toyogirligikg1)
                            inputField.value = value;

                            // Create a label for the input field
                            const label = document.createElement('label');
                            label.innerHTML = key + ' (' + inputName + (rowIndex + 1) + '): ';

                            // Append the label and input field to the container
                            inputContainer.appendChild(label);
                            inputContainer.appendChild(inputField);
                            inputContainer.appendChild(document.createElement('br')); // Add line break
                        });
                    });
                }
            </script>
@endsection
