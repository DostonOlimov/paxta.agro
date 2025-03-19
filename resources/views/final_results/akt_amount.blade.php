@extends('layouts.app')
@section('styles')
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
                        @if($data->isNotEmpty())
                           @foreach($counts as $count)
                                <div class="py-3">
                                    <a href="{{url()->previous()}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> {{ trans("app.Ortga") }}</a>
                                    <button class="btn btn-primary" id="print-invoice-btn-{{ $loop->iteration }}"><i class="fa fa-print"></i> {{ trans("app.Chop etish") }}</button>
                                </div>
                                <div id="invoice-cheque-{{ $loop->iteration }}" class="row" style="padding: 10px;">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div style="font-family: 'Arial Black'; width: 100%; display: flex; font-size: 18px; padding: 5px">
                                                    <div style="font-family: 'Arial Black'; width: 100%; text-align: center; display: inline-block;font-size: 20px;"><b>Paxta tolasi toylarining og‘irlik bo‘yicha xisoboti</b></div>
                                                </div>
                                                <div style="font-family: 'Arial Black'; width: 100%; display: flex; font-size: 16px;">
                                                    <div style="font-family: 'Arial Black'; width: 55%; text-align: left; display: inline-block;"><b>Laboratoriya:</b>{{ $dalolatnoma->test_program->application->decision->laboratory->name }}</div>
                                                    <div style="font-family: 'Arial Black'; width: 10%; text-align: left; display: inline-block;"></div>
                                                    <div style="font-family: 'Arial Black'; width: 34%; text-align: right; display: inline-block;"><b>Sana:</b> {{ date("d.m.Y", strtotime(optional($dalolatnoma->laboratory_final_results)->date)) }}</div>
                                                </div>
                                                <div style="font-family: 'Arial Black'; width: 100%; display: flex; font-size: 16px;">
                                                    <div style="font-family: 'Arial Black'; width: 55%; text-align: left; display: inline-block;"><b>Buyurtmachi:</b>{{ $dalolatnoma->test_program->application->organization->name }}</div>
                                                    <div style="font-family: 'Arial Black'; width: 30%; text-align: left; display: inline-block;"><b>Partiya:</b>{{ $dalolatnoma->test_program->application->crops->party_number }}</div>
                                                    <div style="font-family: 'Arial Black'; width: 15%; text-align: right; display: inline-block;"><b>Shtrix kod:</b></div>
                                                </div>
                                                <div style="font-family: 'Arial Black'; width: 100%; display: flex; font-size: 16px;">
                                                    <div style="font-family: 'Arial Black'; width: 55%; text-align: left; display: inline-block;"><b>Zavod nomi:</b>{{ $dalolatnoma->test_program->application->prepared->name }}-{{ $dalolatnoma->test_program->application->prepared->kod }}</div>
                                                    <div style="font-family: 'Arial Black'; width: 10%; text-align: left; display: inline-block;"></div>
                                                    <div style="font-family: 'Arial Black'; width: 35%; text-align: right; display: inline-block;"><b>
                                                            @foreach($dalolatnoma->gin_balles as $ball)
                                                                {{ $ball->from_number }} dan {{ $ball->to_number }} gacha <br>
                                                            @endforeach
                                                        </b></div>
                                                </div>
                                                <div class="table-responsive row">
                                                    <table style="border:1px black; margin-top:5px; text-align: center; font-size: 14px;" >
                                                        <thead>
                                                        <tr>
                                                            @foreach($data as $dat)
                                                                <th style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.08rem 0.08rem;" >№</th>
                                                                <th style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.08rem 0.08rem;">Og'irligi</th>
                                                            @endforeach
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @for($i = 0; $i < 50; $i++)
                                                            <tr>
                                                                @foreach($data as $dat)
                                                                    @php $index = 50 * ($loop->iteration-1) + $i; @endphp

                                                                    <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.08rem 0.08rem;">{{ $index +1 }}</td>
                                                                    @if(isset($dat[$index]))
                                                                        @if($dat[$index]['class'] == $count->class and $dat[$index]['sort'] == $count->sort)
                                                                            @if($dat[$index]['amount'])
                                                                                <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.08rem 0.08rem;" >
                                                                                    {{$dat[$index]['amount']}} kg
                                                                                </td>
                                                                            @else
                                                                                <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.08rem 0.08rem;">
                                                                                    0 kg
                                                                                </td>
                                                                            @endif
                                                                        @else
                                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.08rem 0.08rem;"></td>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            </tr>
                                                        @endfor

                                                        </tbody>
                                                    </table>
                                                    @php
                                                        $sum_amount = $dalolatnoma->akt_amount()->sum('amount');
                                                    @endphp
                                                    <div style="border:1px solid black; font-family: 'Arial Black'; width: 100%; display: flex; justify-content: space-between; padding:0; font-size: 16px;">
                                                        <div style="border:1px solid black;  width: 20%;text-align: center; display: flex; align-items: center;justify-content: center; ">
                                                            <span> <b>Kip soni:<br> {{ $count->count }} ta  </b></span>
                                                        </div>
                                                        <div style="border:1px solid black;  width: 20%;text-align: center; display: flex; align-items: center;justify-content: center; ">
                                                            <span> <b>Brutto og'irligi:<br> {{ $count->total_amount ? $count->total_amount : 0 }} kg </b></span>
                                                        </div>
                                                        <div style="border:1px solid black;  width: 20%;text-align: center; display: flex; align-items: center;justify-content: center; ">
                                                            <span> <b>Netto og'irligi:<br> {{ $count->total_amount ? $count->total_amount - $count->count * $dalolatnoma->tara : 0 }} kg  </b></span>
                                                        </div>
                                                        <div style="border:1px solid black;  width: 20%;text-align: center; display: flex; align-items: center;justify-content: center; ">
                                                            <span> <b>Tara og'irligi: <br>{{ $count->count * $dalolatnoma->tara }} kg  </b></span>
                                                        </div>
                                                        <div style="border:1px solid black;  width: 20%;text-align: center; display: flex; align-items: center;justify-content: center; ">
                                                            <span> <b>Tara og'irligi(1):<br> {{ $dalolatnoma->tara }} kg  </b></span>
                                                        </div>
                                                    </div>
                                                    <div style="font-family: 'Arial Black'; width: 100%; display: flex; justify-content: space-between; padding-top:10px;font-size: 16px;">
                                                        <span> <b>Izox: Paxta tolasi toylari bo‘yicha og‘irlik xisoboti Buyurtmachi tomonidan taqdim qilingan.  </b></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
                        @foreach($counts as $count)

                        // Bind the click event directly
                        $(`#print-invoice-btn-{{ $loop->iteration }}`).click(function () {
                            $(`#invoice-cheque-{{ $loop->iteration }}`).print({
                                noPrintSelector: '.no-print',
                                title: '',
                            });
                        });
                        @endforeach
                    });
                </script>

@endsection
