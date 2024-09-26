@extends('layouts.app')
@section('styles')
    <style>
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
@endsection
@section('content')
    @can('view', \App\Models\User::class)
        <div class=" content-area ">
            <div class="page-header">
                <h4 class="page-title mb-0" style="color:white">{{ trans('app.Sinov bayonnomasi') }}</h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card p-4">
                        @if($test->test_program->application->crops->name_id == 1)
                            @include('laboratory_protocol._cheque')
                        @elseif($test->test_program->application->crops->name_id == 2)
                            @include('laboratory_protocol._cheque_chigit')
                        @endif

                            {{-- @if ($test->laboratory_results->quality == 1)
                                @include('laboratory_protocol._cheque')
                            @else
                                @include('laboratory_protocol._cheque2')
                            @endif --}}
                        <div class="py-3">
                            <a href="{{ url()->previous() }}" class="btn btn-primary">{{trans("app.Ortga")}}</a>
                            <button class="btn btn-primary" id="print-invoice-btn">{{trans("app.Chop etish")}}</button>
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
                $('#application-date').text(moment(currenttest.laboratory_final_results.date).format('DD.MM.YYYY'))
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
