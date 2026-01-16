@extends('layouts.app')
@section('styles')
    <style>
        #my_table th {
            padding: 5px 5px;
            !important;
            font-weight: bold;
            font-size: 16px;
        }

        #my_table td {
            padding: 5px 5px;
            !important;
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
        @if (session('message'))
            <div class="row massage">
                <div class="col-md-12 col-sm-12">
                    <div class="alert alert-success text-center">
                        <input id="checkbox-10" type="checkbox" checked="">
                        <label for="checkbox-10 colo_success"> {{ session('message') }} </label>
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
                                        <a href="{!! url('/dalolatnoma/search') !!}">
                                            <span class="visible-xs"></span>
                                            <i class="fa fa-list fa-lg">&nbsp;</i> {{ trans('app.Ro\'yxat') }}
                                        </a>
                                    </li>
                                    <li class="active">
                                        <span class="visible-xs"></span>
                                        <i class="fa fa-eye fa-lg">&nbsp;</i>
                                        {{ trans('app.View') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-container">
                                            <div id="invoice-cheque" class="py-4 col-12"
                                                style="font-family: 'Times New Roman'; font-size:18px; line-height:1.5;">

                                                <h2 class="text-center fw-bold"
                                                    style="font-size:28px; margin-bottom:35px; text-transform:uppercase;">
                                                    Namuna olish dalolatnomasi № {{$result->number}}
                                                </h2>

                                                <!-- Sana va joy -->
                                                <div
                                                    style="display:flex; justify-content:space-between; margin-bottom:25px; font-size:20px;">
                                                    <div>{{$date}} yil</div>
                                                    <div><b>{{ $region ?? 'Jizzax viloyati' }}</b></div>
                                                </div>

                                                <!-- Tanlab olingan joy -->
                                                <div style="margin-bottom:18px;">
                                                    Namuna tanlab olingan joy:
                                                    <span
                                                        style="display:inline-block; width:75%; border-bottom:1px solid #000;">{{optional($result->test_program)->application->prepared->name}} - {{optional($result->test_program)->application->prepared->kod}}</span>
                                                </div>

                                                <!-- Buyurtmachi -->
                                                <div style="margin-bottom:18px;">
                                                    Buyurtmachi:
                                                    <span style="text-decoration:underline;">
                                                        {{ optional($result->test_program)->application->organization->name }}
                                                    </span>
                                                </div>

                                                <!-- Shartnoma -->
                                                <div style="margin-bottom:18px;">
                                                    Shartnoma № 
                                                </div>

                                                <!-- Mahsulot saqlanishi -->
                                                <div style="margin-bottom:18px;">
                                                    Mahsulot saqlanishi (turi yoki sharoiti):
                                                    <span
                                                        style="display:inline-block; width:65%; border-bottom:1px solid #000;">&nbsp;</span>
                                                </div>

                                                <!-- Toylar soni -->
                                                <div style="margin-bottom:18px;">
                                                    Tudadagi toylar soni:
                                                    <span
                                                        style="display:inline-block; width:60%; border-bottom:1px solid #000;">№ {{$result->toy_count}}</span>
                                                </div>

                                                <!-- Olingan toylar -->
                                                <div style="margin-bottom:25px;">
                                                    Namuna olingan toylar raqami va soni yoki umumiy miqdori:
                                                    <br><br>
                                                     @foreach($result->gin_balles as $ball)
                                                    <div style="width: 100%; display: flex; justify-content: space-between;">
                                                            <div style="width: 25%; display: inline-block;">
                                                                <h3 >dan: {{$ball->from_number}}</h3>
                                                            </div>
                                                            <div style="width: 25%; display: inline-block;">
                                                                <h3 >gacha: {{$ball->to_number}}</h3    >
                                                            </div>
                                                            <div style="width: 25%; display: inline-block;">
                                                                <h3 >dan: {{$ball->from_toy}}</h3>
                                                            </div>
                                                            <div style="width: 25%; display: inline-block;">
                                                                <h3 >gacha: {{$ball->to_toy}}</h3>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <!-- Nuqsonlar -->
                                                <div style="margin-bottom:25px;">
                                                    Toylarning o‘ralishi va saqlanishdagi nuqsonlar:
                                                    <br><br>
                                                    <div
                                                        style="border-bottom:1px solid #000; width:100%; height:22px; margin-bottom:10px;">
                                                    </div>
                                                    <div
                                                        style="border-bottom:1px solid #000; width:100%; height:22px; margin-bottom:10px;">
                                                    </div>
                                                    <div style="border-bottom:1px solid #000; width:100%; height:22px;">
                                                    </div>
                                                </div>

                                                <!-- Javobgarlik -->
                                                <div style="margin-bottom:35px; font-size:19px;">
                                                    Mahsulot toylarining (yoki umum) og‘irligi ko‘rsatkichlariga buyurtmachi
                                                    <b>javobgardir.</b>
                                                </div>

                                                <!-- Imzo bloklari -->
                                                <div
                                                    style="width:100%; display:flex; justify-content:space-between; margin-top:20px;">
                                                    <div style="width:45%; text-align:center;">
                                                        <div style="border-bottom:1px solid #000; height:25px;"></div>
                                                        <div style="margin-top:5px;">Namuna oluvchi mutaxassis (F.I.Sh)
                                                        </div>
                                                    </div>

                                                    <div style="width:45%; text-align:center;">
                                                        <div style="border-bottom:1px solid #000; height:25px;"></div>
                                                        <div style="margin-top:5px;">Imzo</div>
                                                    </div>
                                                </div>

                                                <br><br>

                                                <div
                                                    style="width:100%; display:flex; justify-content:space-between; margin-top:20px;">
                                                    <div style="width:45%; text-align:center;">
                                                        <div style="border-bottom:1px solid #000; height:25px;"></div>
                                                        <div style="margin-top:5px;">Buyurtmachi yoki uning vakili (F.I.Sh)
                                                        </div>
                                                    </div>

                                                    <div style="width:45%; text-align:center;">
                                                        <div style="border-bottom:1px solid #000; height:25px;"></div>
                                                        <div style="margin-top:5px;">Imzo</div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                        <div class="py-3">
                                            <a href="{{ url()->previous() }}" class="btn btn-warning"><i
                                                    class="fa fa-arrow-left"></i>{{ trans('app.Orqaga') }}</a>
                                            <button class="btn btn-primary" id="print-invoice-btn"><i
                                                    class="fa fa-print"></i> {{ trans('app.Chop etish') }}</button>
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
                    $(document).ready(function() {
                        function printCheque() {
                            $('#invoice-cheque').print({
                                NoPrintSelector: '.no-print',
                                title: '',
                            })
                        }
                        $('#print-invoice-btn').click(function(ev) {
                            printCheque()
                        })
                    });
                </script>
            @endsection
