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
       th{
           font-weight: bold;
       }
        td{
            font-weight: bold;
        }

    </style>
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp Dalolatnoma
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
                                        <a href="{!! url('/akt_amount/edit/'.$id)!!}">
                                        <span class="visible-xs"></span>
                                        <i class="fa fa-edit fa-lg">&nbsp;</i>
                                        {{ trans('app.Edit')}}
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
                                                    <th class="border-bottom-0 border-top-0">Kip №</th>
                                                    <th>Shtrix kod</th>
                                                    <th>Og'irlik (kg)</th>
                                                    <th class="border-bottom-0 border-top-0">#</th>
                                                    <th>Shtrix kod</th>
                                                    <th>Og'irlik (kg)</th>
                                                    <th class="border-bottom-0 border-top-0">#</th>
                                                    <th>Shtrix kod</th>
                                                    <th>Og'irlik (kg)</th>
                                                    <th class="border-bottom-0 border-top-0">#</th>
                                                    <th>Shtrix kod</th>
                                                    <th>Og'irlik (kg)</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <form method="post" enctype="multipart/form-data"
                                                      data-parsley-validate class="form-horizontal form-label-left">
                                                    @csrf
                                                    @php $count = count($results[0]); @endphp
                                                     @for($i = 0; $i < $count; $i++)
                                                        <tr>
                                                            <td>{{ $i+1}}</td>
                                                            <td>{{ $results[0][$i]['shtrix_kod'] }}</td>
                                                                @if($results[0][$i]['amount'])
                                                                <td class="bg-info text-white">
                                                                    {{$results[0][$i]['amount']}} kg
                                                                </td>
                                                                @else
                                                                <td class="bg-danger text-white">
                                                                    0 kg
                                                                </td>
                                                                @endif

                                                            <td>{{ $count + $i+1 }}</td>
                                                            <td>{{$results[1][$i]['shtrix_kod']}}</td>
                                                            @if($results[1][$i]['amount'])
                                                                <td class="bg-info text-white">
                                                                    {{$results[1][$i]['amount']}} kg
                                                                </td>
                                                            @else
                                                                <td class="bg-danger text-white">
                                                                    0 kg
                                                                </td>
                                                            @endif

                                                            <td>{{ 2 * $count + $i +1}}</td>
                                                            <td>{{$results[2][$i]['shtrix_kod']}}</td>
                                                            @if($results[2][$i]['amount'])
                                                                <td class="bg-info text-white">
                                                                    {{$results[2][$i]['amount']}} kg
                                                                </td>
                                                            @else
                                                                <td class="bg-danger text-white">
                                                                    0 kg
                                                                </td>
                                                            @endif

                                                            <td>{{ 3 * $count + $i +1}}</td>
                                                            <td>@if(array_key_exists($i,$results[3])) {{$results[3][$i]['shtrix_kod']}}  @endif</td>

                                                            @if(array_key_exists($i,$results[3]))
                                                                @if($results[3][$i]['amount'])
                                                                    <td class="bg-info text-white">
                                                                        {{$results[3][$i]['amount']}} kg
                                                                    </td>
                                                                @else
                                                                    <td class="bg-danger text-white">
                                                                        0 kg
                                                                    </td>
                                                                @endif
                                                            @else
                                                                <td></td>
                                                            @endif

                                                        </tr>
                                                    @endfor
                                                 </form>
                                                </tbody>
                                            </table>
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
                    }
                });
            }
        }
    </script>
@endsection
