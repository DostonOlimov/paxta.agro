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

        .container-layout {
            padding: 4px 5px 4px 1px;
            max-width: 212px;
            border-top: 2px solid black;
            border-bottom: 2px solid black;
            display: flex;
            align-items: center;
            column-gap: 6px;
            margin: 0 auto;
        }

        .layout-left {
            width: 50%;
            height: 70px;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding-right: 8px;
            border-right: 2px solid #969696;
        }

        .layout-right {
            border-bottom: 2px solid #3f51b5;
            border-top: 2px solid #3f51b5;
            width: 100%;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(5, 1fr);
            grid-column-gap: 0px;
            grid-row-gap: 0px;
        }
        .layout-right-top{
           text-align: right;
        }
        .layout-right-bottom{
            text-align: right;
        }

        .layout-right > div {
            display: grid;
            place-items: center;
        }
        .div1 {
            grid-area: 1 / 2 / 6 / 3;
        }

        .div2 {
            grid-area: 1 / 1 / 2 / 2;
        }
        .div3 {
            grid-area: 1 / 3 / 2 / 4;
        }
        .div4 {
            grid-area: 2 / 1 / 3 / 2;
        }
        .div5 {
            grid-area: 2 / 3 / 3 / 4;
        }
        .div6 {
            grid-area: 3 / 1 / 4 / 2;
        }
        .div7 {
            grid-area: 3 / 3 / 4 / 4;
        }
        .div8 {
            grid-area: 4 / 1 / 5 / 2;
        }
        .div9 {
            grid-area: 4 / 3 / 5 / 4;
        }
        .div10 {
            grid-area: 5 / 1 / 6 / 2;
        }
        .div11 {
            grid-area: 5 / 3 / 6 / 4;
        }

        .layout__arrow {
            width: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .layout__arrow svg {
            width: 15px;
            height: 50px;
        }

        .layout__arrow-up {
            margin-top: -45px;
        }

        .layout__arrow-down {
            margin-bottom: -55px;
            transform: rotate(180deg);
        }

        .my_svg {
            position: absolute;
            z-index: 2;
            top: 1px;
            width: 8px;
            height: 15px;
            border-radius: 25px;
            background-color: #3f51b5;
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
                                        <a href="{!! url('/humidity/search')!!}">
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
                                                <h1 class="text-center fw-bold">Paxta tolasining namligini massaviy nisbatini aniqlash uchun namuna olish</h1>
                                                <h1 class="text-center fw-bold">DALOLATNOMASI № {{$result->number}}</h1>
                                                <h1 class="text-left">{{$date}} yil</h1>
                                                <h2 class="text-left">Korxona nomi {{optional($result->dalolatnoma)->test_program->application->prepared->name}} - {{optional($result->dalolatnoma)->test_program->application->prepared->kod}}</h2>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h2 class="text-left">Na'muna tanlab olish</h2>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h2 class="text-right"><span  class="text-decoration-underline"> O'z DSt 614 Paxta tolasi.Na'muna tanlab olish usullari</span><br>muvofiq amalga oshirildi.</h2>
                                                    </div>
                                                </div>

                                                <h2 class="text-left">Paxta tolasi to'dasi № <span  class="text-decoration-underline">&nbsp;&nbsp;{{$result->party}}&nbsp;&nbsp;</span> &nbsp;&nbsp;, navi <span  class="text-decoration-underline">&nbsp;&nbsp;{{$result->nav}}&nbsp;&nbsp;</span> &nbsp;&nbsp;, sinfi <span  class="text-decoration-underline">&nbsp;&nbsp;{{$result->sinf}}&nbsp;&nbsp;</span></h2>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h2 class="text-left">Seleksion navi <span  class="text-decoration-underline">&nbsp;&nbsp;{{$result->selection->name}}</span></h2>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h2 class="text-right">Ishlab chiqarilgan toy soni  <span  class="text-decoration-underline">&nbsp;&nbsp;{{$result->toy_count}}</span></h2>
                                                    </div>
                                                </div>

                                                <h2 class="text-left">Olingan na'munalar soni <span  class="text-decoration-underline">&nbsp;&nbsp;{{$result->toy_amount}}</span></h2>
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
