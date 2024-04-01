@extends('layouts.front')

@section('content')
        @php $address = $decision->laboratory->full_address  @endphp
        <div class=" content-area ">
            <div class="page-header">
                <h4 class="page-title mb-0" style="color:white">Sertifikatlashtirishni oʼtkazish uchun qaror</h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card p-4">
                        <div class="row">
                            @include('decision._cheque')

                        </div>
                        <div class="py-3">
                            <button class="btn btn-primary" id="print-invoice-btn">Chop etish</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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


            fillCheque()

            $('#print-invoice-btn').click(function (ev) {
                printCheque()
            })
        });
    </script>
@endsection
