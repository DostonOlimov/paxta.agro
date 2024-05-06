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
                                <table id="examples1" class="table table-striped table-bordered " style="margin-top:20px;" >
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{trans('app.Ariza sanasi')}}</th>
                                        <th>{{trans('app.Na\'muna olingan viloyat')}}</th>
                                        <th>{{trans('app.Na\'muna olingan shahar yoki tuman')}}</th>
                                        <th>{{trans('app.Buyurtmachi korxona yoki tashkilot nomi')}}</th>
                                        <th>{{trans('app.Tayorlangan shaxobcha yoki sexning nomi')}}</th>
                                        <th>{{trans('app.Ishlab chiqargan davlat')}}</th>
                                        <th>{{trans('app.Name')}}</th>
                                        <th>{{trans('app.Toʼda (partiya) raqami')}}</th>
                                        <th>{{trans('app.amount')}}</th>
                                        <th>{{trans('app.Hosil yili')}}</th>
                                        <th>{{trans('app.Qaror fayllari')}}</th>
                                        <th>{{trans('app.Sinov bayonnoma fayllari')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $offset = (request()->get('page', 1) - 1) * 50;
                                    @endphp
                                    @foreach($apps as $app)
                                        <tr>
                                            <td>{{$offset + $loop->iteration}}</td>
                                            <td><a href="{!! url('/application/view/'.$app->id) !!}">{{ $app->date }}</a></td>
                                            <td>{{ optional($app->organization)->city->region->name }}</td>
                                            <td>{{ optional($app->organization)->city->name }}</td>
                                            <td><a href="{!! url('/organization/view/'.$app->organization_id) !!}">{{ optional($app->organization)->name }}</a></td>
                                            <td>{{ optional($app->prepared)->name }}</td>
                                            <td>{{ optional($app->crops->country)->name }}</td>
                                            <td>{{ optional($app->crops->name)->name }}</td>
                                            <td>{{ optional($app->crops)->party_number }}</td>
                                            <td>{{ optional($app->crops)->amount_name }}</td>
                                            <td>{{ optional($app->crops)->year }}</td>


                                            <td>@if($app->decision)
                                                <a href="{!! url('/decision/view/'.optional($app->decision)->id) !!}"><button type="button" class="btn btn-round btn-info">{{trans('app.Qaror fayli')}}</button></a>
                                                @endif
                                            </td> <td>@if($app->tests)
                                                    <a href="{!! url('/tests/view/'.$app->tests->id) !!}"><button type="button" class="btn btn-round btn-info">{{trans('app.Sinov dasturi fayli')}}</button></a>
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

