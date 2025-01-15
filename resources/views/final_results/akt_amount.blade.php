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
                        @if($results)
                           @foreach($counts as $count)
                                <div class="py-3">
                                    <a href="{{url()->previous()}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> {{ trans("app.Ortga") }}</a>
                                    <button class="btn btn-primary" id="print-invoice-btn-{{ $loop->iteration }}"><i class="fa fa-print"></i> {{ trans("app.Chop etish") }}</button>
                                </div>
                                <div id="invoice-cheque-{{ $loop->iteration }}" class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div style="font-family: 'Arial Black'; width: 100%; display: flex; font-size: 18px; padding: 5px">
                                                    <div style="font-family: 'Arial Black'; width: 100%; text-align: center; display: inline-block;font-size: 20px;"><b>Paxta tolasi toylarining og‘irlik bo‘yicha xisoboti</b></div>
                                                </div>
                                                <div style="font-family: 'Arial Black'; width: 100%; display: flex; font-size: 16px;">
                                                    <div style="font-family: 'Arial Black'; width: 55%; text-align: left; display: inline-block;"><b>Laboratoriya:</b>{{ $dalolatnoma->test_program->application->decision->laboratory->name }}</div>
                                                    <div style="font-family: 'Arial Black'; width: 10%; text-align: left; display: inline-block;"></div>
                                                    <div style="font-family: 'Arial Black'; width: 34%; text-align: right; display: inline-block;"><b>Sana:</b> {{ optional($dalolatnoma->laboratory_final_results)->date }}</div>
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
                                                    <table style="border:1px black; margin-top:15px; text-align: center; font-size: 14px;" >
                                                        <thead>
                                                        <tr>
                                                            @foreach($results as $data)
                                                                <th style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem;" >№</th>
                                                                <th style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem;">Og'irligi</th>
                                                            @endforeach
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @for($i = 0; $i < 50; $i++)
                                                            <tr>
                                                                @foreach($results as $data)
                                                                    <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem;">{{ 50 * ($loop->iteration-1) + $i +1 }}</td>
                                                                    @if(isset($data[$i]))
                                                                        @if($data[$i]['class'] == $count->class and $data[$i]['sort'] == $count->sort)
                                                                            @if($data[$i]['amount'])
                                                                                <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem;" >
                                                                                    {{$data[$i]['amount']}} kg
                                                                                </td>
                                                                            @else
                                                                                <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem;">
                                                                                    0 kg
                                                                                </td>
                                                                            @endif
                                                                        @else
                                                                            <td style=" font-family: 'Arial Black'; border: 1px solid black;  padding: 0.1rem 0.1rem;"></td>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            </tr>
                                                        @endfor
                                                        </tbody>
                                                    </table>
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
