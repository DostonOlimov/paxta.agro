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

        .input-container i.fa-pencil {
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
                                                        <th>Shtrix kod</th>
                                                        <th>Og'irlik (kg)</th>
                                                    @endforeach
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <form method="post" enctype="multipart/form-data"
                                                      data-parsley-validate class="form-horizontal form-label-left">
                                                    @csrf
                                                    @for($i = 0; $i < 50; $i++)
                                                        <tr>
                                                            @foreach($data1 as $data)
                                                            <td>{{ 50 * ($loop->iteration-1) + $i +1 }}</td>
                                                            <td>@if(isset($data[$i])) {{$data[$i]['shtrix_kod']}}  @endif</td>
                                                            <td>
                                                                @if(isset($data[$i]))
                                                                    <div class="input-container">
                                                                        <input type="number" step="0.1" class="form-control" name="amount" id="amount{{$data[$i]['id']}}"
                                                                               onchange="saveAnswer({{$data[$i]['id']}} , this)"  value="{{$data[$i]['amount']}}" @if($data[$i]['amount']) {{'disabled'}} @endif>
                                                                        @if($data[$i]['amount']) <i class="fa fa-pencil" onclick="changeDisplay(this,{{$data[$i]['id']}})"></i> @endif
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            @endforeach
                                                        </tr>

                                                    @endfor
                                                </form>
                                                </tbody>
                                            </table>
                                        </div>
                                        <a  class="p-2" href="{!! url('/akt_amount/view/'.$id) !!}"><button type="button" class="btn btn-round btn-success">&nbsp;&nbsp;{{ trans('app.Submit')}}&nbsp;&nbsp;</button></a>
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
                function saveAnswer(id,elm) {
                    if (elm.value > 0){
                        $.ajax({
                            type: 'POST',
                            url: '{{ route('save.amount') }}',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: id,
                                amount: elm.value
                            },
                            success: function(response) {
                                elm.setAttribute("disabled", "disabled");
                                const pencilIcon = document.createElement('i');
                                pencilIcon.classList.add('fa', 'fa-pencil');
                                pencilIcon.setAttribute('onclick', 'changeDisplay(this,'+id+')');
                                elm.parentNode.appendChild(pencilIcon);
                                // Find the next input element
                                var nextId = id + 1;
                                var nextInput = document.getElementById('amount' + nextId);

                                // If the next input element exists, set focus on it
                                if (nextInput) {
                                    nextInput.focus();
                                }
                            }
                        });
                    }
                }

            </script>
@endsection
