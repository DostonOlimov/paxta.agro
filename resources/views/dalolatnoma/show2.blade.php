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
        .underline-space {
            flex-grow: 1;
            border-bottom: 1px solid black; /* Creates the underline */
            margin-left: 10px; /* Adjust space before underline as needed */
        }

        .flex-container {
            display: flex;
            align-items: center;
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
                                            <div id="invoice-cheque" class="py-4 col-12" style=" font-family: Times New Roman;">
                                                <h1 class="text-center">"Qishloq xo'jaligi mahsulotlari sifatini baholash markazi" davlat muassasi</h1>
                                                <h2 class="text-center">Namuna olish va identifikatlash dalolatnomasi </h2>
                                                <h2 class="text-center">{{$date}} yil</h2>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h2 class="text-left">Ishlab chiqaruvchi nomi: {{optional($result->test_program)->application->organization->name}}</h2>
                                                    </div>
                                                </div>

                                                <h2 class="flex-container">
                                                    DM “Markaz” vakili tomonidan:
                                                    <span class="underline-space"></span>
                                                </h2>

                                                <h2 class="flex-container">
                                                    Korxona vakillari ishtirokida:
                                                    <span class="underline-space"></span>
                                                </h2>
                                                <h2>
                                                    <span style="text-decoration: underline">&nbsp;&nbsp;O’z DSt 596:2014 ;   UzTR 99-007:2016 &nbsp;&nbsp;</span>talablariga
                                                    muvofiqligini tekshirish uchun tayyor maxsulotdan namunalar tanlab olindi
                                                </h2>
                                                <h2>Namuna quyidagi MX asosida tanlab olindi ___
                                                    <span style="text-decoration: underline">&nbsp;&nbsp; &nbsp;&nbsp;O’zDSt 598:2008 п 5.1; п 5.3   ГОСТ_10852-86 п 2.2.1; п 2.2.2
                                                    </span>
                                                </h2>
                                                <h2>Qo‘shimcha ma'lumotlar:    seleksion nav:
                                                    <span style="text-decoration: underline">{{$result->selection->name}} probootbornik vositasida  olindi
                                                    </span>
                                                </h2>
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th rowspan="2">Tekshirilayotgan mahsulot nomi	</th>
                                                        <th rowspan="2">O‘lchov birligi	</th>
                                                        <th rowspan="2">To‘da raqami	</th>
                                                        <th rowspan="2">To‘da miqdori</th>
                                                        <th rowspan="2">Ishlab chiqarish sanasi</th>
                                                        <th colspan="2">Olingan namunalar miqdori)</th>
                                                    </tr>
                                                    <tr>
                                                        <td>tashqi ko‘rinish uchun</td>
                                                        <td>Sinov uchun  </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Literlangan texnik chigit</td>
                                                        <td>tonna</td>
                                                        <td>{{$result->party}}</td>
                                                        <td>{{$result->toy_count}}</td>
                                                        <td>{{$result->test_program->application->crops->year}} yil hosilidan</td>
                                                        <td>{{$result->amount2}}</td>
                                                        <td>{{$result->amount}}</td>
                                                    </tr>

                                                </table>
                                                <h5>Namuna  ishlab chiqaruvchi tarozisida o‘lchangan.</h5>
                                                <h2>Namuna olingan to‘dani, sinov natijalari olingunga qadar tashishga ruxsat etilmaydi.</h2>
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
