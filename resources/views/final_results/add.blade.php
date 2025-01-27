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
                        @if($results != 0)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive row">
                                                <table id="examples1" class="table table-striped table-bordered nowrap" style="margin-top:20px;" >
                                                    <thead>
                                                    <tr>
                                                        <th>Yakuniy natija fayli</th>
                                                        <th>To'dadagi toylar soni (dona)</th>
                                                        <th>Og'irligi(kg)</th>
                                                        <th>Sort</th>
                                                        <th>Sinf</th>
                                                        <th>Harakat</th>

                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                  @php $amount = 0; @endphp
                                                        @foreach ($counts as $count)
                                                            <tr>
                                                                <td>
                                                                    @if(session('crop') == 1)
                                                                        @if(!$count->certificate)
                                                                            <a href="{!! url('/final_results/add2/'. $count->id) !!}"><button type="button" class="btn btn-round btn-success">{{ trans('app.Qo\'shish')}}</button></a>
                                                                        @else
                                                                            <span class="txt_color">
                                                                                @if(\App\Models\Sertificate::find($count->certificate->id)->attachment)
                                                                                    <a href="{{route('attachment.download', ['id' => $count->certificate->attachment->id])}}" class="text-azure">
                                                                                    <i class="fa fa-download"></i> Asos fayli
                                                                                        </a>
                                                                                @endif
                                                                            </span>
                                                                        @endif
                                                                    @else
{{--                                                                        @if(!$count->certificate)--}}
{{--                                                                            <a href="{!! url('sertificate-protocol/sertificate-view2/' . $count->id) !!}"><button type="button"--}}
{{--                                                                                                                                                               class="btn btn-round btn-info">--}}
{{--                                                                                    <i class="fa fa-eye"></i> {{ trans('app.View') }}</button></a>--}}
{{--                                                                        @else--}}
{{--                                                                            <span class="txt_color">--}}
{{--                                                                                @if(\App\Models\Sertificate::find($count->certificate->id)->attachment)--}}
{{--                                                                                    <a href="{{route('attachment.download', ['id' => $count->certificate->attachment->id])}}" class="text-azure">--}}
{{--                                                                                    <i class="fa fa-download"></i> Asos fayli--}}
{{--                                                                                        </a>--}}
{{--                                                                                @endif--}}
{{--                                                                            </span>--}}
{{--                                                                        @endif--}}
                                                                    @endif
                                                                </td>
                                                                <td> {{ $count->count}}</td>
                                                                <td> {{ $count->amount ? $count->amount - $count->count * $dalolatnoma->tara : 0}}</td>
                                                                <td> {{ $count->sort}}</td>
                                                                <td> {{ optional(\App\Models\CropsGeneration::where('kod','=',$count->class)->first())->name}}</td>

                                                                <td>
                                                                    <a href="{!! url('/final_results/update/'.$count->id) !!}"><button type="button" class="btn btn-round btn-success">Yangilash</button></a>
                                                                </td>
                                                            </tr>
                                                            @php $amount +=  $count->amount @endphp
                                                        @endforeach
                                                        <tr>
                                                            <td >Jami:</td>
                                                            <td>{{$dalolatnoma->toy_count}}</td>
                                                            <td>{{$amount}}</td>
                                                            <td colspan="7"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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
