@extends('layouts.app')
@section('styles')
    <style>
        th {
            background-color: #2381c5 !important; /* gradient from orange to dark orange */
            color: white !important;
            font-weight: bold !important;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #eaf2ee; /* Light Gray */
        }
        .table-striped tbody tr:nth-of-type(even) {
            background-color: #ffffff; /* Lighter Gray */
        }
    </style>
@endsection
@section('content')
    <!-- page content -->
    <?php $userid = Auth::user()->id; ?>
    @can('viewAny', \App\Models\Application::class)
        <div class="section">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp Barcha arizalar bo'yicha umumiy ro'yxat
                    </li>
                </ol>
            </div>
            <!-- filter component -->
            <x-filter :crop="$crop" :city="$city" :from="$from" :till="$till"  />
            <!--filter component -->

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="examples1" class="table table-striped table-bordered " style="margin-top:20px;" >
                                    <thead>
                                    <tr>
                                        <th rowspan="2" class="border-bottom-0 border-top-0">#</th>
                                        <th rowspan="2">Ariza raqami</th>
                                        <th rowspan="2">Ariza sanasi</th>
                                        <th rowspan="2">Na'muna olingan viloyat</th>
                                        <th rowspan="2">Na'muna olingan shahar yoki tuman</th>
                                        <th rowspan="2">Buyurtmachi korxona yoki tashkilot nomi</th>
                                        <th rowspan="2">Urugʼlik tayorlangan shaxobcha yoki sexning nomi</th>
                                        <th rowspan="2">Ishlab chiqargan davlat</th>
                                        <th rowspan="2">Ekin turi</th>
                                        <th rowspan="2">Ekin navi</th>
                                        <th rowspan="2">Ekin avlodi</th>
                                        <th rowspan="2">Toʼda (partiya) raqami</th>
                                        <th rowspan="2">Ekin miqdori</th>
                                        <th rowspan="2">Hosil yili</th>
                                        <th rowspan="2">Sinov bayonnoma raqami</th>
                                        <th colspan="2">Sertifikat</th>
                                        <th colspan="3">Tahlil natija</th>
                                        <th rowspan="2">Izoh</th>
                                        <th rowspan="2">Qaror fayllari</th>
                                        <th rowspan="2">Sinov bayonnoma fayllari</th>
                                        <th rowspan="2">Yakuniy natija fayli</th>
                                    </tr>
                                    <tr>
                                        <th>Reestr raqami</th>
                                        <th>Berilgan sanasi</th>
                                        <th>Raqami</th>
                                        <th>Berilgan sanasi</th>
                                        <th>Yaroqliligi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $offset = (request()->get('page', 1) - 1) * 50;
                                    @endphp
                                    @foreach($apps as $app)
                                        <tr>
                                            @php $type = optional(optional($app->tests)->result)->type ; @endphp
                                            <td>{{$offset + $loop->iteration}}</td>
                                            <td><a href="{!! url('/application/view/'.$app->id) !!}">{{ $app->app_number }}</a></td>
                                            <td><a href="{!! url('/application/view/'.$app->id) !!}">{{ $app->date }}</a></td>
                                            <td>{{ optional($app->organization)->city->region->name }}</td>
                                            <td>{{ optional($app->organization)->city->name }}</td>
                                            <td><a href="{!! url('/organization/view/'.$app->organization_id) !!}">{{ optional($app->organization)->name }}</a></td>
                                            <td>{{ optional($app->prepared)->name }}</td>
                                            <td>{{ optional($app->crops->country)->name }}</td>
                                            <td>{{ optional($app->crops->name)->name }}</td>
                                            <td>{{ optional($app->crops->type)->name }}</td>
                                            <td>{{ optional($app->crops->generation)->name }}</td>
                                            <td>{{ optional($app->crops)->party_number }}</td>
                                            <td>{{ optional($app->crops)->amount_name }}</td>
                                            <td>{{ optional($app->crops)->year }}</td>
                                            <td>@if($type == 2){{ optional(optional($app->tests)->result)->number }}@else @if(is_null($type)) <button class="btn btn-warning">Jarayonda</button>@endif @endif</td>
                                            <td>{{ optional(optional(optional($app->tests)->result)->certificate)->reestr_number }}</td>
                                            <td>{{ optional(optional(optional($app->tests)->result)->certificate)->given_date }}</td>
                                            <td>@if($type != 2 ){{ optional(optional($app->tests)->result)->number }}@endif</td>
                                            <td>@if($type != 2 ){{ optional(optional($app->tests)->result)->date }}@endif</td>
                                            <td>
                                                @if($type === 1 ){{ 'Muvofiq' }}@endif
                                                @if($type === 0 ){{ 'Nomuvofiq' }}@endif
                                            </td>
                                            <td>{{ optional(optional($app->tests)->result)->comment }}</td>
                                            <td>@if($app->decision)
                                                <a href="{!! url('/decision/view/'.optional($app->decision)->id) !!}"><button type="button" class="btn btn-round btn-info">Qaror fayli</button></a>
                                                @endif
                                            </td> <td>@if($app->tests)
                                                    <a href="{!! url('/tests/view/'.$app->tests->id) !!}"><button type="button" class="btn btn-round btn-info">Sinov dasturi fayli</button></a>
                                                @endif
                                            </td>
                                            <td> @if(optional(optional($app->tests)->result)->attachment)
                                                    <a href="{{route('attachment.download', ['id' => optional(optional($app->tests)->result)->attachment->id])}}" class="text-azure">
                                                        <i class="fa fa-download"></i> Yuklash
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{ $apps->links() }}
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
                    <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp {{ trans('app.You Are Not Authorize This page.')}}</span>
                </div>
            </div>
        </div>
    @endcan
    <!-- /page content -->
    <script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>

@endsection
