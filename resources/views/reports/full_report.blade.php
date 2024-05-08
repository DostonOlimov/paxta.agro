@extends('layouts.app')
@section('styles')
    <style>
        th {
            background-color: #2381c5 !important;
            /* gradient from orange to dark orange */
            color: white !important;
            font-weight: bold !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #eaf2ee;
            /* Light Gray */
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #ffffff;
            /* Lighter Gray */
        }
        .filter-button {
            margin-left: 0;
        }

        .table-responsive {
            transform: rotate(180deg);
            direction: rtl;
        }

        .table-responsive::-webkit-scrollbar {
            transform: rotate(180deg);
            direction: ltr;
            height: 16px;
        }

        .table-responsive table {
            transform: rotate(180deg);
            direction: initial;
        }

        .table-responsive nav .pagination {
            padding-top: 13px;
            direction: initial;
            transform: rotate(180deg);
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
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp{{ trans('app.Barcha arizalar bo\'yicha umumiy ro\'yxat') }}
                    </li>
                </ol>
            </div>
            <!-- filter component -->
            <x-filter :crop="$crop" :city="$city" :from="$from" :till="$till" />
            <!--filter component -->

            <div class="row">
                <div class="col-sm-3 pt-2" style=" margin-top: -46px; margin-bottom: 13px;">
                    <a class="btn btn-success" style="color: white"
                        href="{{ route('excel.export', [
                            'from' => $from,
                            'till' => $till,
                            'city' => $city,
                            'crop' => $crop,
                        ]) }}">
                        <i class="fa fa-file-excel-o"
                            style="margin-right: 6px; color: white;"></i>{{ trans('app.Excel fayl') }}</a>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="examples1" class="table table-striped table-bordered " style="margin-top:20px;">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">#</th>
                                            <th rowspan="2">{{ trans('app.Ariza sanasi') }}</th>
                                            <th rowspan="2">{{ trans('app.Dalolatnoma raqami') }}</th>
                                            <th rowspan="2">{{ trans('app.Na\'muna olingan viloyat') }}</th>
                                            <th rowspan="2">{{ trans('app.Na\'muna olingan shahar yoki tuman') }}</th>
                                            <th rowspan="2">{{ trans('app.Buyurtmachi korxona yoki tashkilot nomi') }}</th>
                                            <th rowspan="2">{{ trans('app.Tayorlangan shaxobcha yoki sexning nomi') }}</th>
                                            <th rowspan="2">{{ trans('app.Ishlab chiqargan davlat') }}</th>
                                            <th rowspan="2">{{ trans('app.Name') }}</th>
                                            <th rowspan="2">{{ trans('app.Toʼda (partiya) raqami') }}</th>
                                            <th rowspan="2">{{ trans('app.amount') }}</th>
                                            <th rowspan="2">{{ trans('app.Hosil yili') }}</th>
                                            <th rowspan="2">To'dadagi toylar soni (dona)</th>
                                            <th rowspan="2">Jami og'irlik(kg)</th>
                                            <th rowspan="2">Sof Og'irlik(kg)</th>
                                            <th colspan="8" style="text-align: center">Sifat nazorati natijalari</th>
                                            <th rowspan="2">{{ trans('app.Qaror fayllari') }}</th>
                                            <th rowspan="2">{{ trans('app.Sinov bayonnoma fayllari') }}</th>
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
                                        @if (isset($results))
                                            @foreach ($results as $result)
                                                <tr>
                                                    <td>{{ $offset + $loop->iteration }}</td>
                                                    <td><a
                                                            href="{!! url('/application/view/' . $result->test_program->application->id) !!}">{{ $result->test_program->application->date }}</a>
                                                    </td>
                                                    <td>{{ optional($result->dalolatnoma)->number }}</td>
                                                    <td>{{ __('message.' . optional($result->test_program->application->organization)->city->region->name) }}
                                                    </td>
                                                    <td>{{ optional($result->test_program->application->organization)->city->name }}
                                                    </td>
                                                    <td><a
                                                            href="{!! url('/organization/view/' . $result->test_program->application->organization_id) !!}">{{ optional($result->test_program->application->organization)->name }}</a>
                                                    </td>
                                                    <td>{{ optional($result->test_program->application->prepared)->name }}</td>
                                                    <td>{{ optional($result->test_program->application->crops->country)->name }}
                                                    </td>
                                                    <td>{{ optional($result->test_program->application->crops->name)->name }}
                                                    </td>
                                                    <td>{{ optional($result->test_program->application->crops)->party_number }}
                                                    </td>
                                                    <td>{{ optional($result)->amount ? $result->amount . ' kg' : '' }} </td>
                                                    <td>{{ optional($result->test_program->application->crops)->year }}</td>

                                                    <td> {{ $result->count }}</td>
                                                    <td> {{ $result->amount }}</td>
                                                    <td> {{ $result->amount != null ? $result->amount - $result->count * optional(optional($result->dalolatnoma->test_program->application)->prepared)->tara : '' }}
                                                    </td>
                                                    <td> 4</td>
                                                    <td> {{ $result->sort }}</td>
                                                    <td> {{ optional(\App\Models\CropsGeneration::where('kod', '=', $result->class)->first())->name }}
                                                    </td>
                                                    <td> {{ round($result->staple) }}</td>
                                                    <td> {{ round($result->mic, 1) }}</td>
                                                    <td> {{ round($result->strength, 1) }}</td>
                                                    <td> {{ round($result->uniform, 1) }}</td>
                                                    <td> {{ round($result->humidity, 2) }}</td>


                                                    <td>
                                                        @if ($result->test_program->application->decision)
                                                            <a href="{!! url('/decision/view/' . optional($result->test_program->application->decision)->id) !!}"><button type="button"
                                                                    class="btn btn-round btn-info">{{ trans('app.Qaror fayli') }}</button></a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($result->test_program->application->tests)
                                                            <a href="{!! url('/tests/view/' . $result->test_program->id) !!}"><button type="button"
                                                                    class="btn btn-round btn-info">{{ trans('app.Sinov dasturi fayli') }}</button></a>
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                {{ $results->links() }}
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
                    <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp
                        {{ trans('app.You Are Not Authorize This page.') }}</span>
                </div>
            </div>
        </div>
    @endcan
    <!-- /page content -->
    <script src="{{ URL::asset('vendors/jquery/dist/jquery.min.js') }}"></script>

@endsection
