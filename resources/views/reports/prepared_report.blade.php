@extends('layouts.app')
@section('content')
    <!-- page content -->
    <?php $userid = Auth::user()->id; ?>
    @can('viewAny', \App\Models\User::class)
        <div class="section">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp {{ trans("message.Ishlab chiqaruvchi zavodlar kesimda ma'lumot") }}
                    </li>
                </ol>
            </div>
            @if (session('message'))
                <div class="row massage">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="alert alert-success text-center">
                            @if (session('message') == 'Successfully Submitted')
                                <label for="checkbox-10 colo_success"> {{ trans('app.Successfully Submitted') }}</label>
                            @elseif(session('message') == 'Successfully Updated')
                                <label for="checkbox-10 colo_success"> {{ trans('app.Successfully Updated') }} </label>
                            @elseif(session('message') == 'Successfully Deleted')
                                <label for="checkbox-10 colo_success"> {{ trans('app.Successfully Deleted') }} </label>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            <!-- filter component -->
            <x-filter :crop="$crop" :city="$city" :from="$from" :till="$till" />
            <!--filter component -->

            <div class="row">
                {{-- <div class="col-sm-3 pt-2" style=" margin-top: -46px; margin-bottom: 13px;">
                    <button onclick="printTable()" class="btn btn-primary">{{trans("app.Chop etish")}}</button>
                    <a class="btn btn-success" style="color: white"
                       href="{{ route('export.company', [
                            'from' => $from?? ($_GET['from']??''),
                            'till' => $till?? ($_GET['till']??''),
                            'city' => $city?? ($_GET['city']??''),
                            'crop' => $crop?? ($_GET['crop']??''),
                        ]) }}">
                        <i class="fa fa-file-excel-o"
                           style="margin-right: 6px; color: white;"></i>{{ trans('app.Excel fayl') }}</a>
                </div> --}}

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered nowrap myTable" style="margin-top:20px;">
                                    <thead>
                                        <tr>
                                            <th style="width: 0.1%"></th>
                                            <th style="width: 25%">{{trans('app.Toʼda (partiya) raqami')}}</th>
                                            <th>{{ trans("app.Bo'lak partiya") }}</th>
                                            <th>{{trans('app.Sertifikat reestr raqami')}}</th>
                                            <th>{{ trans('app.Sertifikat sanasi') }}</th>
                                            <th>{{ trans('app.Sort') }}</th>
                                            <th>{{ trans('app.Sinf') }}</th>
                                            <th>{{ trans('app.Kip soni') }}</th>
                                            <th>{{ trans("app.Jami og'irlik(kg)") }}</th>
                                            <th>{{ trans("app.Sof Og'irlik(kg)") }}</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        @php
                                            $son = 0;
                                            $check = 0;
                                        @endphp
                                        @foreach ($prepareds as $key => $prepared)
                                            @foreach ($prepared as $inside_key => $box)
                                                <tr>
                                                    <td style="font-weight: 700; font-size: 15px" colspan="10">
                                                        &nbsp
                                                        <span style="text-decoration: underline;">{{ $inside_key }}</span>
                                                        <span style="text-decoration: underline;">{{ $key }}</span>
                                                    </td>
                                                </tr>

                                                @foreach ($box as $item)
                                                    <tr style="text-align: unset">
                                                        <td></td>
                                                        {{-- <td>{{ $item->amount }}</td> --}}
                                                        <td>{{ optional($item->dalolatnoma->test_program->application->crops)->party_number }}
                                                        </td>
                                                        <td>
                                                            <?php
                                                            if ($check == $item->dalolatnoma->test_program->application->crops->party_number) {
                                                                echo ++$son;
                                                            } else {
                                                                $check = $item->dalolatnoma->test_program->application->crops->party_number;
                                                                $son = 0;
                                                                echo $son;
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>{{ optional($item->certificate)->reestr_number}}</td>
                                                        <td>{{ \Carbon\Carbon::parse(optional($item->certificate)->given_date)->format('d.m.Y')}}</td>
                                                        <td>{{ $item->sort }}</td>
                                                        <td>{{ $item->generation->name }}</td>
                                                        <td>{{ $item->count }}</td>
                                                        <td> {{ optional($item)->amount ? $item->amount . ' kg' : '' }}</td>
                                                        <td> {{ $item->amount != null ? $item->amount - $item->count * optional($item->dalolatnoma)->tara : '' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <h4
                            style="position: sticky; bottom: 0; padding: 1%; color: #0052cc; width: 100%; display: flex; justify-content: space-between; background-color: white">
                            <span>{{($totalSum)? trans("app.Jami og'irlik(kg)").': '.number_format($totalSum, 2, ',', ' '):''}}</span>
                        </h4>
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
