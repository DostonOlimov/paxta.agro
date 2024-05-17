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
                        <i class="fe fe-life-buoy mr-1"></i>&nbsp {{trans("message.Korxonalar kesimda ma'lumot")}}
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
                <div class="col-sm-3 pt-2" style=" margin-top: -46px; margin-bottom: 13px;">
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

                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered nowrap myTable"
                                    style="margin-top:20px;">
                                    <thead>
                                        <tr>
                                            <th class="border-bottom-0 border-top-0">#</th>
                                            <th>{{trans('app.Zavod kodi')}}</th>
                                            <th>{{ trans('app.Buyurtmachi tashkilot nomi') }}</th>
                                            <th>{{ trans('app.Kip soni') }}</th>
                                            <th>{{ trans('app.Massasi') }}</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        @php
                                            $offset = (request()->get('page', 1) - 1) * 50;
                                        @endphp
                                        @foreach ($companies as $company)
                                            <tr>
                                                <td>{{ $offset + $loop->iteration }}</td>
                                                <td> {{ $company->kod }}</td>
                                                <td><a
                                                        href="{!! url('/organization/view/' . $company->id) !!}">{{ $company->name }}</a>
                                                </td>
                                                <td>{{ $company->kip }}</td>
                                                <td>{{ round(($company->netto/1000),4) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $companies->links() }}
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

    <script>
        function printTable() {
           var table = document.querySelector('.myTable');
           var content = table.outerHTML;

           var printWindow = window.open('', '', 'height=600,width=800');
           printWindow.document.write('<html><head><title>Print Table</title>');

           printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
           printWindow.document.write('</head><body>');
           printWindow.document.write(content);
           printWindow.document.write('</body></html>');
           printWindow.document.close();

           printWindow.print();
       }
       </script>

@endsection
