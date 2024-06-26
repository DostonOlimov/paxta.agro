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
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp{{trans('app.Barcha arizalar bo\'yicha umumiy ro\'yxat')}}
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
                                <table class="table table-striped table-bordered " style="margin-top:20px;" >
                                    <thead>
                                    <tr>
                                        <th rowspan="2">#</th>
                                        <th rowspan="2">{{trans('app.Ariza sanasi')}}</th>
                                        <th rowspan="2">{{trans('app.Na\'muna olingan viloyat')}}</th>
                                        <th rowspan="2">{{trans('app.Na\'muna olingan shahar yoki tuman')}}</th>
                                        <th rowspan="2">{{trans('app.Buyurtmachi korxona yoki tashkilot nomi')}}</th>
                                        <th rowspan="2">{{trans('app.Tayorlangan shaxobcha yoki sexning nomi')}}</th>
                                        <th rowspan="2">Zavod raqami</th>
                                        <th rowspan="2">{{trans('app.Name')}}</th>
                                        <th rowspan="2">{{trans('app.Toʼda (partiya) raqami')}}</th>
                                        <th rowspan="2">{{trans('app.Hosil yili')}}</th>
                                        <th rowspan="2">Partiya raqami</th>
                                        <th rowspan="2">To'dadagi toylar soni (dona)</th>
                                        <th rowspan="2">Jami og'irlik(kg)</th>
                                        <th rowspan="2">Sof Og'irlik(kg)</th>
                                        <th colspan="8>" style="text-align: center">Sifat nazorati natijalari</th>
                                        <th rowspan="2">{{trans('app.Ishlab chiqargan davlat')}}</th>
                                        <th rowspan="2">{{trans('app.Qaror fayllari')}}</th>
                                        <th rowspan="2">{{trans('app.Sinov bayonnoma fayllari')}}</th>
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
                                    @php
                                        $offset = (request()->get('page', 1) - 1) * 50;
                                    @endphp
                                    @foreach($apps as $app)
                                        <tr>
                                            <td>{{$offset + $loop->iteration}}</td>
                                            <td><a href="{!! url('/application/view/'.$app->test_program->app_id) !!}">{{ $app->test_program->application->date }}</a></td>
                                            <td>{{ optional($app->test_program->application->organization)->city->region->name }}</td>
                                            <td>{{ optional($app->test_program->application->organization)->city->name }}</td>
                                            <td><a href="{!! url('/organization/view/'.$app->test_program->application->organization_id) !!}">{{ optional($app->test_program->application->organization)->name }}</a></td>
                                            <td>{{ optional($app->test_program->application->prepared)->name }}</td>
                                            <td>{{optional(optional($app->test_program->application)->prepared)->kod}}</td>
                                            <td>{{ optional($app->test_program->application->crops->name)->name }}</td>
                                            <td>{{ optional($app->test_program->application->crops)->party_number }}</td>
                                            <td>{{ optional($app->test_program->application->crops)->year }}</td>

                                            <td>{{optional(optional($app->test_program->application)->crops)->party_number}}</td>
                                            <td> {{ $app->count}}</td>
                                            <td> {{ $app->amount}}</td>
                                            <td> {{ $app->amount - $app->count * optional(optional($app->test_program->application)->prepared)->tara}}</td>
                                            <td> 4</td>
                                            <td> {{ $app->sort}}</td>
                                            <td> {{ optional(\App\Models\CropsGeneration::where('kod','=',$app->class)->first())->name}}</td>
                                            <td> {{ round($app->staple)}}</td>
                                            <td> {{ round($app->mic,1)}}</td>
                                            <td> {{ round($app->strength,1)}}</td>
                                            <td> {{ round($app->uniform,1)}}</td>
                                            <td> {{ round(($app->humidity/10),1)}}</td>

                                            <td>{{ optional($app->test_program->application->crops->country)->name }}</td>
                                            <td>@if($app->test_program->application->decision)
                                                    <a href="{!! url('/decision/view/'.optional($app->test_program->application->decision)->id) !!}"><button type="button" class="btn btn-round btn-info">{{trans('app.Qaror fayli')}}</button></a>
                                                @endif
                                            </td> <td>@if($app->test_program)
                                                    <a href="{!! url('/tests/view/'.$app->test_program_id) !!}"><button type="button" class="btn btn-round btn-info">{{trans('app.Sinov dasturi fayli')}}</button></a>
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
