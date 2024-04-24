@extends('layouts.app')
@section('styles')
    <style>
        #my_table th {
            padding: 5px 5px ;!important;
            font-weight: bold;
            font-size: 16px;
        }
        #my_table td {
            padding: 5px 5px ;!important;
            font-size: 16px;
        }
    </style>
@endsection
@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp Muvofiqlik bo'yicha xulosa
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
                                        <a href="{!! url('/humidity_result/search')!!}">
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-container">
                                            <div id="invoice-cheque" class="py-4 col-12" style=" font-family: Times New Roman;">
                                                <h1 class="text-center fw-bold"> {{optional($result->dalolatnoma)->test_program->application->decision->laboratory->name}}</h1>
                                                <h1 class="text-center fw-bold">Paxta tolasi namligining massaviy nisbatini aniklash {{$result->number}}-sonli DАLOLАTNOMАSI</h1>
                                                <h1 class="text-left">{{$date}} yil</h1>
                                                <h2 class="text-left">Na'muna olingan joy: {{optional($result->dalolatnoma)->test_program->application->prepared->name}} - {{optional($result->dalolatnoma)->test_program->application->prepared->kod}}</h2>
                                                <h2 class="text-left">Ishlab chiqarilgan korxona nomi: {{optional($result->dalolatnoma)->test_program->application->organization->name}}</h2>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h2 class="text-left">Sinovlar</h2>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h2 class="text-right"><span  class="text-decoration-underline"> O'z DSt 614 Paxta tolasi.Na'muna tanlab olish usullari</span><br>muvofiq amalga oshirildi.</h2>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h2 class="text-left">Paxta tolasi to'dasi № <span>&nbsp;&nbsp;{{$result->dalolatnoma->humidity->party}}&nbsp;&nbsp;</span> &nbsp;&nbsp;</h2>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h2 class="text-left">Seleksion navi <span >&nbsp;&nbsp;{{$result->dalolatnoma->humidity->selection->name}}</span></h2>
                                                    </div>
                                                </div>
                                                <table class="table table-bordered text-center" style="font-weight: bold;">
                                                    <tr>
                                                        <th rowspan="2">Paxta tolasi to'dasi</th>
                                                        <th colspan="2">Na'mlikning massaviy nisbati,%</th>

                                                    </tr>
                                                    <tr>
                                                        <th>Me'yor,%</th>
                                                        <th>Amalda</th>
                                                    </tr>
                                                    <tr>
                                                        @php
                                                            $value1 = 100 * ($result->m0-$result->mk0)/$result->mk0 - 0.4;
                                                            $value2 = 100 * ($result->m1-$result->mk1)/$result->mk1 - 0.4;
                                                        @endphp
                                                        <td>{{$result->dalolatnoma->humidity->party}}</td>
                                                        <td>5.0 - 8.5</td>
                                                        <td>{{round(($value1 + $value2) / 2 , 2) }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                    </div>
                                        <div class="py-3">
                                            <a href="{{url()->previous()}}" class="btn btn-warning"><i class="fa fa-arrow-left"></i>{{trans('app.Orqaga')}}</a>
                                            <button class="btn btn-primary" id="print-invoice-btn"><i class="fa fa-print"></i> Chop etish</button>
                                        </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            function printCheque() {
                $('#invoice-cheque').print({
                    NoPrintSelector: '.no-print',
                    title: '',
                })
            }
            $('#print-invoice-btn').click(function (ev) {
                printCheque()
            })
        });
    </script>
@endsection
