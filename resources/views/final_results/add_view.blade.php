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
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp Yakuniy natijalar
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
                        <div id="invoice-cheque">
                            <h4>Laboratoriya :
                                {{ optional($dalolatnoma->test_program->application->decision->laboratory)->name }}</h4>
                            <h4>Buyurtmachi : {{ optional($dalolatnoma->test_program->application)->prepared->name }}</h4>
                            <h4>Sertifikatlanuvchi mahsulot :
                                {{ optional($dalolatnoma->test_program->application)->crops->name->name }}</h4>
                            @if ($results != 0)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="table-responsive row">
                                                    <table id="examples1" class="table table-striped table-bordered nowrap" style="margin-top:20px;" >
                                                        <thead>
                                                            <tr>
                                                                <th rowspan="2">Zavod raqami</th>
                                                                <th rowspan="2">Partiya raqami</th>
                                                                <th rowspan="2">To'dadagi toylar soni (dona)</th>
                                                                <th rowspan="2">Jami og'irlik(kg)</th>
                                                                <th rowspan="2">Sof Og'irlik(kg)</th>
                                                                <th colspan="8>" style="text-align: center">Sifat nazorati natijalari</th>
                                                            </tr>
                                                            <tr>
                                                                <th>Tip</th>
                                                                <th>Sort</th>
                                                                <th>Sinf</th>
                                                                <th>Shtaple uzunligi</th>
                                                                <th>Mikroneyr</th>
                                                                <th>Solishtirma uzunlik kuchi</th>
                                                                <th>Uzunligi bo'yicha bir xillik ko'rsatkichi,%</th>
                                                                <th>Namlik ko'rsatkichi,%</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $amount = 0; @endphp
                                                            @foreach ($counts as $count)
                                                                <tr>
                                                                    <td>{{optional(optional($dalolatnoma->test_program->application)->prepared)->kod}}</td>
                                                                    <td>{{optional(optional($dalolatnoma->test_program->application)->crops)->party_number}}</td>
                                                                    <td> {{ $count->count}}</td>
                                                                    <td> {{ $count->amount}}</td>
                                                                    <td> {{ $count->amount - $count->count * $dalolatnoma->tara}}</td>
                                                                    <td> 4</td>
                                                                    <td> {{ $count->sort}}</td>
                                                                    <td> {{ optional(\App\Models\CropsGeneration::where('kod','=',$count->class)->first())->name}}</td>
                                                                    <td> {{ round($count->staple)}}</td>
                                                                    <td> {{ round($count->mic,1)}}</td>
                                                                    <td> {{ round($count->strength,1)}}</td>
                                                                    <td> {{ round($count->uniform,1)}}</td>
                                                                    <td> {{ round(($count->humidity),2)}}</td>
                                                                </tr>
                                                                @php $amount +=  $count->amount @endphp
                                                            @endforeach
                                                            <tr>
                                                                <td colspan="2">Jami:</td>
                                                                <td>{{$dalolatnoma->toy_count}}</td>
                                                                <td>{{$amount}}</td>
                                                                <td colspan="9"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="py-3">
                            <a href="{{ url()->previous() }}" class="btn btn-warning"><i
                                    class="fa fa-arrow-left"></i>{{ trans('app.Orqaga') }}</a>
                            <button class="btn btn-primary" id="print-invoice-btn"><i class="fa fa-print"></i>
                                {{ trans('app.Chop etish') }}</button>
                        </div>
                    @else
                        <div class="section" role="main">
                            <div class="card">
                                <div class="card-body text-center">
                                    <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp {{trans('app.Laboratoriya ma\'lumotlari hali yuklanmagan')}}</span>
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
