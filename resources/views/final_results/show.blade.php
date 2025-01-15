@extends('layouts.app')

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
                        <div id="invoice-cheque" class="row" style="background-color: white !important;">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div style="width: 100%; display: flex; font-size: 18px; padding-top: 5px">
                                            <div style="font-family: 'Arial Black'; width: 35%;padding-top:20px; text-align: left; display: inline-block;font-size: 22px;"><b>SINOV NATIJASI HISOBOTI</b></div>
                                                <div style="width: 25%;padding-top:20px; text-align: right; display: inline-block;">
                                                </div>

                                            <div style="font-family: 'Arial Black';  width: 39%; display: inline-block;text-align: right">
                                                <b>{{ $dalolatnoma->test_program->application->decision->laboratory->name }} boshlig‘i
                                                    <span style="font-family: 'Arial Black'; padding: 5px; display: block">
                                                    {{ optional(optional($dalolatnoma->laboratory_final_results)->director)->lastname . '. ' . substr( optional(optional($dalolatnoma->laboratory_final_results)->director)->name, 0, 1) }}</span>
                                                </b>
                                            </div>
                                        </div>
                                        <div style="width: 100%; display: flex; font-size: 16px;">
                                            <div style="font-family: 'Arial Black';  width: 25%; text-align: left; display: inline-block;">Partiya raqami: {{ optional(optional(optional($dalolatnoma->test_program)->application)->crops)->party_number }}</div>
                                            <div style="font-family: 'Arial Black'; width: 25%; text-align: left; display: inline-block;">Kip soni: {{$dalolatnoma->toy_count}}</div>
                                            <div style="font-family: 'Arial Black'; width: 25%; text-align: left; display: inline-block;">Mikroneyr: {{ round($mic, 1) }}</div>
                                            <div style="font-family: 'Arial Black'; width: 24%; display: inline-block;text-align: left">Uzunlik: {{ round($length) / 100 }}</div>
                                        </div>
                                        <div style="width: 100%; display: flex; font-size: 16px;">
                                            <div style="font-family: 'Arial Black'; width: 50%; text-align: left; display: inline-block;">Maxsus uzilish og'irligi, gf/tex: {{ round($strength, 1) }}</div>
                                            <div style="font-family: 'Arial Black'; width: 49%; text-align: left; display: inline-block;">Uzunligi bo'yicha bir xillik ko'rsatkichi, %: {{ round($uniform, 1) }}</div>
                                        </div>
                                        <div class="table-responsive row">
                                            <table  style="
                                            border: 1px solid black;
                                            border-collapse: collapse;
                                            width: 100%;
                                            table-layout: fixed;
                                            white-space: nowrap;
                                            margin-top:20px;
                                            background-color: white !important;
                                            text-align: center;
                                            font-size: 12px;

" >
                                                <thead>
                                                <tr >
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">№</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.15rem 0.1rem; font-weight: bold;">Kip <br>raqami</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">Nav</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">Sinf</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">№</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.15rem 0.1rem; font-weight: bold;">Kip <br>raqami</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">Nav</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">Sinf</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">№</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.15rem 0.1rem; font-weight: bold;">Kip <br>raqami</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">Nav</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">Sinf</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">№</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.15rem 0.1rem; font-weight: bold;">Kip <br>raqami</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">Nav</th>
                                                    <th style="font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">Sinf</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <form method="post" enctype="multipart/form-data"
                                                      data-parsley-validate class="form-horizontal form-label-left">
                                                    @csrf
                                                    @php $count = count($results[0]); @endphp
                                                    @for($i = 0; $i < $count; $i++)
                                                        <tr>
                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem;" class="nuber_column">{{ $i+1}}</td>
                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">{{ $results[0][$i]['gin_bale'] }}</td>
                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">{{ $results[0][$i]['sort'] }}</td>
                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">{{ $results[0][$i]['class'] }}</td>

                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;" class="nuber_column">{{ $count + $i+1 }}</td>
                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">{{$results[1][$i]['gin_bale']}}</td>
                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">{{$results[1][$i]['sort']}}</td>
                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">{{$results[1][$i]['class']}}</td>


                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;" class="nuber_column">{{ 2 * $count + $i +1}}</td>
                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">{{$results[2][$i]['gin_bale']}}</td>
                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">{{$results[2][$i]['sort']}}</td>
                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">{{$results[2][$i]['class']}}</td>


                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;" class="nuber_column">{{ 3 * $count + $i +1}}</td>
                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">@if(array_key_exists($i,$results[3])) {{$results[3][$i]['gin_bale']}}  @endif</td>

                                                            @if(array_key_exists($i,$results[3]))
                                                                @if($results[3][$i]['class'])
                                                                    <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">
                                                                        {{$results[3][$i]['sort']}}
                                                                    </td>
                                                                    <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;">
                                                                        {{$results[3][$i]['class']}}
                                                                    </td>
                                                                @endif
                                                            @else
                                                                <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;"></td>
                                                                <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem; font-weight: bold;"></td>
                                                            @endif

                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                            <div class="data-section mb-3 p-3" style="font-family: 'Arial Black'; font-size: 18px;">
                                               <b>
                                                   {{"Jami :"}}
                                                        @foreach ($counts as $count)
                                                            {{" {$count->sort}/ {$count->class} = {$count->count}\n ta"}}
                                                        @endforeach
                                               </b>
                                            </div>
                                            <div style="font-family: 'Arial Black';width: 100%; display: flex; justify-content: space-between; padding-top:10px;font-size: 16px;">
                                                <div style="width: 49%; display: inline-block;">
                                                        <span> <b>Paxta mahsuloti sifatini tasniflash
                                                            bo‘yicha mutaxassis (klasser)  </b></span>
                                                </div>
                                                <div style="width: 50%; display: inline-block;text-align: center">
                                                        <span>
                                                            {{ optional(optional($dalolatnoma->laboratory_final_results)->klassiyor)->name }}
                                                        </span>
                                                </div>
                                            </div>
                                            <div style="font-family: 'Arial Black';width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 16px;">
                                                <div style="width: 49%; display: inline-block;">
                                                    <span> <b>Texnologik qurilmalar operatori (HVI)  </b></span>
                                                </div>
                                                <div style="width: 50%; display: inline-block;text-align: center;">
                                                    <span> {{ optional(optional($dalolatnoma->laboratory_final_results)->operator)->name }}</span>
                                                </div>
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
