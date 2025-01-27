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
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp Na'muna olish dalolatnomasi
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
                                        <a href="{!! url('/dalolatnoma/search')!!}">
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
                                            <div id="invoice-cheque" class="py-4 col-12" style=" font-family: Times New Roman; ">
                                                <h1 class="text-center" style="font-size: 30px;">"Qishloq xo'jaligi mahsulotlari sifatini baholash markazi" davlat muassasining</h1>
                                                <h1 class="text-center" style="font-size: 30px;">{{optional($result->test_program)->application->decision->laboratory->name}}</h1>
                                                <h1 class="text-center fw-bold" style="font-size: 30px;">Paxta tolasini na'muna olish dalolatnomasi № {{$result->number}}</h1>


                                                <div style="width: 100%; display: flex; justify-content: space-between; font-size: 18px;">
                                                    <div style="width: 50%; display: inline-block;">
                                                        <h1 class="text-left">Tanlov joyi: {{optional($result->test_program)->application->organization->name}}</h1>
                                                    </div>
                                                    <div style="width: 50%; display: inline-block;">
                                                        <h1 class="text-center">{{$date}} yil</h1>
                                                    </div>
                                                </div>

                                                <h1 class="text-left">Ishlab chiqaruvchi nomi : <span  class="text-decoration-underline">&nbsp;&nbsp;{{optional($result->test_program)->application->prepared->name}} - {{optional($result->test_program)->application->prepared->kod}}</span></h1>
                                                <h1 class="text-left">Paxta tolasi to'dasi p/x № <span  class="text-decoration-underline">&nbsp;&nbsp;{{$result->party}}&nbsp;&nbsp;</span> &nbsp;&nbsp;, navi p/x <span  class="text-decoration-underline">&nbsp;&nbsp;{{$result->nav}}&nbsp;&nbsp;</span> &nbsp;&nbsp;, sinfi p/x <span  class="text-decoration-underline">&nbsp;&nbsp;{{$result->sinf}}&nbsp;&nbsp;</span></h1>

                                                <div style="width: 100%; display: flex; justify-content: space-between; font-size: 18px;">
                                                    <div style="width: 50%; display: inline-block;">
                                                        <h1 class="text-left">Seleksion navi <span  class="text-decoration-underline">&nbsp;&nbsp;{{$result->selection->name}}</span></h1>
                                                    </div>
                                                    <div style="width: 50%; display: inline-block;">
                                                        <h1 class="text-left">Jamlangan hajmdagi toylar soni  <span  class="text-decoration-underline">&nbsp;&nbsp;{{$result->toy_count}}</span></h1>
                                                    </div>
                                                </div>
                                                <div style="width: 100%; display: flex; justify-content: space-between; font-size: 18px;">
                                                    <div style="width: 50%; display: inline-block;">
                                                        <h1 class="text-left">Shtrix kod raqami:</h1>
                                                    </div>
                                                    <div style="width: 50%; display: inline-block;">
                                                        <h1 class="text-left">Toylar ketma-ketligi:</h1>
                                                    </div>
                                                </div>


                                                    @foreach($result->gin_balles as $ball)
                                                        <div style="width: 100%; display: flex; justify-content: space-between; font-size: 18px;">
                                                            <div style="width: 25%; display: inline-block;">
                                                                <h1 >dan: {{$ball->from_number}}</h1>
                                                            </div>
                                                            <div style="width: 25%; display: inline-block;">
                                                                <h1 >gacha: {{$ball->to_number}}</h1>
                                                            </div>
                                                            <div style="width: 25%; display: inline-block;">
                                                                <h1 >dan: {{$ball->from_toy}}</h1>
                                                            </div>
                                                            <div style="width: 25%; display: inline-block;">
                                                                <h1 >gacha: {{$ball->to_toy}}</h1>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                <div style="width: 100%; display: flex; justify-content: space-between; font-size: 18px;">
                                                    <div style="width: 50%; display: inline-block;">
                                                        <h1 class="text-left">Olingan na'munalar soni : <span  class="text-decoration-underline">&nbsp;&nbsp;{{$result->toy_count}}</span></h1>
                                                    </div>
                                                    <div style="width: 50%; display: inline-block;">
                                                        <h1 class="text-left">Olingan na'munalar massasi,kg : <span  class="text-decoration-underline">&nbsp;&nbsp;{{$result->amount}}</span></h1>
                                                    </div>
                                                </div>

                                                <div style="width: 100%; display: flex; justify-content: space-between; font-size: 18px;">
                                                    <div style="width: 30%; display: inline-block;">
                                                        <h1 >Paxta tozalash korxonasidagi jamoa rahbari:</h1>
                                                    </div>
                                                    <div style="width: 33%; display: inline-block;">
                                                        <h1 style="border-bottom: 1px solid black; margin:10% 10% 0 "></h1>
                                                        <h1 class="text-center">F.I.O</h1>
                                                    </div>
                                                    <div style="width: 33%; display: inline-block;">
                                                        <h1 style="border-bottom: 1px solid black; margin:10% 25% 0 "></h1>
                                                        <h1 class="text-center">IMZO</h1>
                                                    </div>
                                                </div>

                                                <div style="width: 100%; display: flex; justify-content: space-between; font-size: 18px;">
                                                    <div style="width: 30%; display: inline-block;">
                                                        <h1 >Paxta tozalash korxonasidagi na'muna oluvchi:</h1>
                                                    </div>
                                                    <div style="width: 33%; display: inline-block;">
                                                        <h1 style="border-bottom: 1px solid black; margin:10% 10% 0 "></h1>
                                                        <h1 class="text-center">F.I.O</h1>
                                                    </div>
                                                    <div style="width: 33%; display: inline-block;">
                                                        <h1 style="border-bottom: 1px solid black; margin:10% 25% 0 "></h1>
                                                        <h1 class="text-center">IMZO</h1>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="py-3">
                                            <a href="{{url()->previous()}}" class="btn btn-warning"><i class="fa fa-arrow-left"></i>{{trans('app.Orqaga')}}</a>
                                            <button class="btn btn-primary" id="print-invoice-btn"><i class="fa fa-print"></i> {{trans("app.Chop etish")}}</button>
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
