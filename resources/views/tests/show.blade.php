@extends('layouts.app')

@section('content')
    @can('view', \App\Models\User::class)
    <div class=" content-area ">
        <div class="page-header">
            <h4 class="page-title mb-0 text-white">Sinov dasturi</h4>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card p-4">
                    <div class="row">
                            @include('tests._cheque')
                    </div>
                    <div class="py-3">
                        <a href="{{url()->previous()}}" class="btn btn-primary">{{trans("app.Ortga")}}</a>
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
                    <span class="titleup text-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp {{ trans('app.You Are Not Authorize This page.')}}</span>
                </div>
            </div>
        </div>
    @endcan
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            function fillCheque() {
                $('#director-name').text((currentdecision.application.decision.director.name.charAt(0)).concat(".",currentdecision.application.decision.director.lastname))
                $('#application-date').text(moment(currentdecision.date).format('DD.MM.YYYY'))
                $('#application-year').text(moment(currentdecision.date).format('YYYY'))
                $('#application-month').text(moment(currentdecision.date).format('MM'))
                $('#application-day').text(moment(currentdecision.date).format('DD'))

            }
            function printCheque() {
                $('#invoice-cheque').print({
                    NoPrintSelector: '.no-print',
                    title: '',
                })
            }
            let currentdecision = @json($decision);

            fillCheque()

            $('#print-invoice-btn').click(function (ev) {
                printCheque()
            })
        });
    </script>
@endsection
