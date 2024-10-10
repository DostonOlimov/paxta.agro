@extends('layouts.front')
@section('content')
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: #f4f6f9;
        }
        .first-table td {
            width: 20%;
            text-align: center;
            padding: 5px;
            border: 1px solid #ddd;
        }
        table, th, td{
            border: 1px solid black ;
        }
        td{
            font-weight: normal ;
        }
    </style>
    @can('view', \App\Models\User::class)
        <div class="section" style="margin-top: 140px;">
        <div class=" content-area ">
            <div class="page-header">
                <h4 class="page-title mb-0" style="color:white">Sifat sertifikati</h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card p-4">
                        @include('sifat_sertificate._cheque')
                        <div class="py-3">
                            <a href="{{ url()->previous() }}" class="btn btn-primary">ortga</a>
                            <button class="btn btn-primary" id="print-invoice-btn">Chop etish</button>
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            function fillCheque() {
                $('#application-date').text(moment(currenttest.date).format('DD.MM.YYYY'))
            }

            function printCheque() {
                $('#invoice-cheque').print({
                    NoPrintSelector: '.no-print',
                    title: '',
                })
            }

            let currenttest = @json($test);


            fillCheque()

            $('#print-invoice-btn').click(function(ev) {
                printCheque()
            })
        });
    </script>
@endsection
