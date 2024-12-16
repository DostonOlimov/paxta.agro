@extends('layouts.app')
@section('styles')
    <style>
        .data-section {
            margin-bottom: 1rem; /* Replaces mb-3 */
            padding: 1rem; /* Replaces p-3 */
            background-color: #3498db;
            color: #ffffff;
            font-size: large;
            border-radius: 8px;
        }

        .data-section .row {
            display: flex;
            flex-wrap: wrap;
            margin-left: -0.5rem; /* Align columns */
            margin-right: -0.5rem;
        }

        .data-section .col-md-3,
        .data-section .col-md-6 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            box-sizing: border-box;
        }

        .data-section .col-md-3 {
            flex: 0 0 25%; /* Replaces col-md-3 */
            max-width: 25%;
        }

        .data-section .col-md-6 {
            flex: 0 0 50%; /* Replaces col-md-6 */
            max-width: 50%;
        }

        @media (max-width: 768px) {
            .data-section .col-md-3,
            .data-section .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
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
        .nuber_column{
            background-color: yellow !important;
        }

    </style>
@endsection

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans('message.Yakuniy natijalar')}}
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
                                        <a href="{!! url('/final_results/search')!!}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Ro\'yxat')}}
                                        </a>
                                    </li>
                                    <li class="active">
                                        <span class="visible-xs"></span>
                                        <i class="fa fa-eye fa-lg">&nbsp;</i>
                                        {{ trans('app.View')}}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @if($results)
                            <div class="py-3">
                                <a href="{{url()->previous()}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> {{ trans("app.Ortga") }}</a>
                                <button class="btn btn-primary" id="print-invoice-btn"><i class="fa fa-print"></i> {{ trans("app.Chop etish") }}</button>
                            </div>
                        <div id="invoice-cheque" class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="data-section">
                                            <div class="row">
                                                <div class="col-md-3"></div>
                                                <div class="col-md-3"></div>
                                                <div class="col-md-3"></div>
                                                <div class="col-md-3"></div>
                                                <div class="col-md-6"></div>
                                                <div class="col-md-6"></div>
                                            </div>
                                        </div>
                                        <table>
                                            <tr>
                                                <td>Partiya raqami: {{ optional(optional(optional($dalolatnoma->test_program)->application)->crops)->party_number }}</td>
                                                <td>Kip soni: {{$dalolatnoma->toy_count}}</td>
                                                <td>Mikroneyr: {{ round($mic, 1) }}</td>
                                                <td>Uzunlik: {{ round($length) / 100 }}</td>
                                            </tr>
                                            <tr>
                                                <td>Maxsus uzilish og'irligi, gf/tex: {{ round($strength, 1) }}</td>
                                                <td>Uzunligi bo'yicha bir xillik ko'rsatkichi, %: {{ round($uniform, 1) }}</td>
                                            </tr>
                                        </table>
                                        <div class="table-responsive row">
                                            <table id="examples1" class="table table-striped table-bordered nowrap" style="margin-top:20px;" >
                                                <thead>
                                                <tr>
                                                    <th >№</th>
                                                    <th>Kip raqami</th>
                                                    <th>Nav</th>
                                                    <th>Sinf</th>
                                                    <th class="border-bottom-0 border-top-0">№</th>
                                                    <th>Kip raqami</th>
                                                    <th>Nav</th>
                                                    <th>Sinf</th>
                                                    <th class="border-bottom-0 border-top-0">№</th>
                                                    <th>Kip raqami</th>
                                                    <th>Nav</th>
                                                    <th>Sinf</th>
                                                    <th class="border-bottom-0 border-top-0">№</th>
                                                    <th>Kip raqami</th>
                                                    <th>Nav</th>
                                                    <th>Sinf</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <form method="post" enctype="multipart/form-data"
                                                      data-parsley-validate class="form-horizontal form-label-left">
                                                    @csrf
                                                    @php $count = count($results[0]); @endphp
                                                    @for($i = 0; $i < $count; $i++)
                                                        <tr>
                                                            <td class="nuber_column">{{ $i+1}}</td>
                                                            <td>{{ $results[0][$i]['gin_bale'] }}</td>
                                                            <td>{{ $results[0][$i]['sort'] }}</td>
                                                            <td>{{ $results[0][$i]['class'] }}</td>

                                                            <td class="nuber_column">{{ $count + $i+1 }}</td>
                                                            <td>{{$results[1][$i]['gin_bale']}}</td>
                                                            <td>{{$results[1][$i]['sort']}}</td>
                                                            <td>{{$results[1][$i]['class']}}</td>


                                                            <td class="nuber_column">{{ 2 * $count + $i +1}}</td>
                                                            <td>{{$results[2][$i]['gin_bale']}}</td>
                                                            <td>{{$results[2][$i]['sort']}}</td>
                                                            <td>{{$results[2][$i]['class']}}</td>


                                                            <td class="nuber_column">{{ 3 * $count + $i +1}}</td>
                                                            <td>@if(array_key_exists($i,$results[3])) {{$results[3][$i]['gin_bale']}}  @endif</td>

                                                            @if(array_key_exists($i,$results[3]))
                                                                @if($results[3][$i]['class'])
                                                                    <td>
                                                                        {{$results[3][$i]['sort']}}
                                                                    </td>
                                                                    <td>
                                                                        {{$results[3][$i]['class']}}
                                                                    </td>
                                                                @endif
                                                            @else
                                                                <td></td>
                                                                <td></td>
                                                            @endif

                                                        </tr>
                                                    @endfor
                                                </form>
                                                </tbody>
                                            </table>
                                            <div class="data-section mb-3 p-3" style="background-color: #3498db; color: #ffffff; font-size: large; border-radius: 8px;">
                                                {{"Jami :"}}
                                                @foreach ($counts as $count)
                                                    {{" {$count->sort}/ {$count->class} = {$count->count}\n ta"}}
                                                @endforeach
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
                                        <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp Laboratoriya ma'lumotlari hali yuklanmagan</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

@endsection
            @section('scripts')
                <script>
                    $(document).ready(function () {
                        function fillCheque() {

                        }
                        function printCheque() {
                            $('#invoice-cheque').print({
                                NoPrintSelector: '.no-print',
                                title: '',
                            })
                        }

                        fillCheque()

                        $('#print-invoice-btn').click(function (ev) {
                            printCheque()
                        })
                    });
                </script>
@endsection
