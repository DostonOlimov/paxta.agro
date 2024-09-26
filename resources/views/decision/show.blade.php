@extends('layouts.app')

@section('content')
    @can('view', \App\Models\User::class)
        @php $address = $decision->laboratory->address_kod . ' ' . $decision->laboratory->full_address  @endphp
    <div class=" content-area ">
        <div class="page-header">
            <h4 class="page-title mb-0" style="color:white">Qaror</h4>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card p-4">
                    <div class="row">
                            @include('decision._cheque')

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
                $('#director-name').text((currentdecision.director.name.charAt(0)).concat(".",currentdecision.director.lastname))
                $('#application-date').text(moment(currentdecision.date).format('DD.MM.YYYY'))
                $('#application-year').text(moment(currentdecision.date).format('YYYY'))
                $('#application-month').text(moment(currentdecision.date).format('MM'))
                $('#application-day').text(moment(currentdecision.date).format('DD'))
                $('#application-id').text(currentdecision.number)
                $('#application-organization').text(currentdecision.application.organization.name)
                $('#crop-name').text(currentdecision.application.crops.name.name)
                $('#sxeme-number').text(currentdecision.application.crops.sxeme_number)
                $('#crop-tnved').text(currentdecision.application.crops.kodtnved)

                $('#laboratory-address').text(address)
                $('#laboratory-certificate').text(currentdecision.laboratory.certificate)
                $('#laboratory-name').text(currentdecision.laboratory.name)

                $('#nds-name').text(currentdecision.application.crops.name.nds.name)
                $('#nds-number').text(currentdecision.application.crops.name.nds.number)
                $('#nds-type').text(nds_type)
            }
            function printCheque() {
                $('#invoice-cheque').print({
                    NoPrintSelector: '.no-print',
                    title: '',
                })
            }

            let currentdecision = @json($decision);
            let nds_type = @json($nds_type);
            let address = @json($address);


            fillCheque()

            $('#print-invoice-btn').click(function (ev) {
                printCheque()
            })
        });
    </script>
@endsection
