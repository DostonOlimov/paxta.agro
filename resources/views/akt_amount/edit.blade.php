@extends('layouts.app')

@section('content')
    <style>
        .right_side .table_row, .member_right .table_row {
            border-bottom: 1px solid #dedede;
            float: left;
            width: 100%;
            padding: 1px 0px 4px 2px;
        }

        .table_row .table_td {
            padding: 8px 8px !important;
        }
        .input-container {
            position: relative;
            display: inline-block;
        }

        .input-container input[type="number"] {
            padding-right: 30px; /* Adjust this value as needed to make space for the icon */
        }

        .input-container i.pencil {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
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
                                    <li class="btn-success">
                                        <a href="{!! url('/akt_amount/excel/'.$id)!!}">
                                            <span ></span>
                                            <i class="fa fa-file-excel-o  text-white">&nbsp; Excel orqali yuklash</i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
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
                                                                    {{ $data[$i]['id'] }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if(isset($data[$i]))
                                                                    <div class="input-container">
                                                                        <input type="text" step="0.1" class="form-control" name="amount" id="amount{{$data[$i]['id']}}"  oninput="formatNumber({{$data[$i]['id']}})"  myattr = {{$loop->iteration}}
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
            <script>
                function changeDisplay(elm,id) {
                    document.getElementById('amount'+id).removeAttribute('disabled');
                    elm.remove();
                }
            </script>
            <script>
                function formatNumber(number) {

                    let numberInput = document.getElementById('amount'+number);
                    let value = numberInput.value.replace(/\D/g, ''); // Remove non-numeric characters
                    let formattedValue = '';

                   if (value.length === 4) {
                        formattedValue = value.substring(0, 3) + '.' + value.substring(3);
                        // Find the next input element
                        var nextId = number + 1;
                        var nextInput = document.getElementById('amount' + nextId);

                        // If the next input element exists, set focus on it
                        if (nextInput) {
                            // numberInput.value = numberInput.value / 10;
                            nextInput.removeAttribute('disabled');
                            nextInput.focus();
                        }else{
                            let elm = document.getElementById('amount' + number);
                            saveAnswer(number,elm,elm.getAttribute('myattr'));
                        }
                    } else  {
                       if((value.length === 3)){
                           formattedValue = value.substring(0, 2) + '.' + value.substring(2);
                       }else{
                           formattedValue = value;
                       }
                    }


                    numberInput.value = formattedValue;
                }
                function saveAnswer(id,elm,row) {
                    if (elm.value > 0){
                        let amount = elm.value;
                        if(elm.value.length === 5){
                             amount = 10 * elm.value;
                        }

                        $.ajax({
                            type: 'POST',
                            url: '{{ route('save.amount') }}',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: id,
                                amount: amount
                            },
                            success: function(response) {
                                elm.setAttribute("disabled", "disabled");
                                const pencilIcon = document.createElement('i');
                                pencilIcon.classList.add('fa', 'fa-pencil','pencil');
                                pencilIcon.setAttribute('onclick', 'changeDisplay(this,'+id+')');
                                elm.parentNode.appendChild(pencilIcon);
                                //calculate sum of each row
                                let rowSum = document.getElementById('sum' + row);
                                let newSum = parseFloat(rowSum.value);
                                rowSum.value = newSum + amount;

                            }
                        });
                    }
                }
            </script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    document.getElementById("myForm").addEventListener("submit", function(event) {
                        event.preventDefault(); // Prevent default form submission
                    });

                    // Prevent form submission when Enter key is pressed
                    document.getElementById("myForm").addEventListener("keydown", function(event) {
                        if (event.key === "Enter") {
                            event.preventDefault();
                            return false;
                        }
                    });
                });
            </script>
@endsection
