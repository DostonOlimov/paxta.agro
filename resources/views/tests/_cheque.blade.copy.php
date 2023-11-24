<div id="invoice-cheque" class="py-4 col-12 {{$classes ?? ''}}">
    <h5 class="text-center">Oʼzbekiston Respublikasi Vazirlar Mahkamasi huzuridagi Аgrosanoat majmui ustidan nazorat qilish <br>Inspektsiya qoshidagi “Аgrosanoat majmuida xizmatlar koʼrsatish markazi” davlat unitar korxonasining
        Qishloq xoʼjalik ekinlari urugʼlarini sertifikatlashtirish boʼlimi<br>
        Toshkent viloyati, Qibray tumani, Bobur koʼchasi 1-uy.</h5>
    <h4 class="text-right">
       <b> «TАSDIQLАYMАN» </b><br>
        Qishloq xoʼjalik ekinlari<br>
        urugʼlarini sertifikatlashtirish<br>
        boʼlimi boshligʼi<br>
        _________<span id="director-name"></span><br>
        <span id="application-date"></span> yil

    </h4>
    <b>
        <h4 class="text-center">Qishloq xoʼjalik ekinlari urugʼlarini sertifikatlashtirish boʼyicha<br></h4>
    <h2 class="text-center">SINOV DASTURI</h2></b>
    <div>
        <h4>
            1.  {{ $decision->application->crops->year}}- yil xosilidan ,
            <span id="crop-type"></span> navli,&nbsp; <span id="crop-generation"></span>avlodli &nbsp;
            urugʼlik &nbsp; <span id="crop-name"></span> &nbsp; mahsuloti &nbsp;<br/>
            <span id="nds-type"></span>&nbsp;  <span id="nds-number"></span>&nbsp;<span id="nds-name"></span>
            talablariga muvofiq,<br/> sifat koʼrsatkichlarini sinovdan oʼtkazish quyidagi standartlardagi usullarni qoʼllagan xolda amalga oshirilsin.
        </h4>
    </div>
    <h4>2.Namunaning identifikatsiya raqami va vazni:</h4>
    <div>
        <table class="table table-bordered align-middle">
            <tr>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
            </tr>
            <tr >
                <td colspan="4"> Har bir sinov na'munasining vazni
                    &nbsp;&nbsp;<span id="weight"></span>
                    &nbsp;<span id="measure-type"></span>
                </td>
                <td></td>
            </tr>
        </table>
    </div>
    @php $t = 1; @endphp
    <h4>3.Sifat ko‘rsatkichlari bo‘yicha me’yoriy hujjatlar:</h4>
    <table style="font-weight: bold" class="table table-bordered align-middle">

        @foreach($indicators as $k => $indicator)
            <tr>
                <td>@if(!$indicator->indicator->parent_id) {{$t}} @endif</td>
                @if($indicator->indicator->parent_id)
                    {{$indicator->indicator->child->parent_id }}
                @endif
                <td>{{$indicator->indicator->name}}</td>
                <td>{!! nl2br($indicator->indicator->nd_name) !!}</td>
                @if($indicator->indicator->nd_name)
                    <td> +</td>
                @endif
            </tr>
            @if(!$indicator->indicator->parent_id) @php $t=$t+1; @endphp @endif
        @endforeach
    </table>
    <div>
        Aloxida yozuvlar : &nbsp;{{ $decision->extra_data }}
    </div>

</div>

<script>
    function printCheque() {
        $('#invoice-cheque').print({
            NoPrintSelector: '.no-print',
            title: '',
        })
    }

    function fillCheque() {
        $('#director-name').text((currentInvoice.director.name.charAt(0)).concat(".",currentInvoice.director.display_name.charAt(0),".",currentInvoice.director.lastname))
        $('#application-date').text(moment(currentInvoice.application.date).format('DD.MM.YYYY'))
        $('#year').text(currentInvoice.application.crops.year)
        $('#application-id').text(currentInvoice.app_id)
        $('#crop-name').text(currentInvoice.application.crops.name.name)
        $('#crop-type').text(currentInvoice.application.crops.type.name)
        $('#crop-generation').text(currentInvoice.application.crops.generation.name)
        $('#measure-type').text(measure_type)
        $('#count').text(currentInvoice.count)
        $('#weight').text(currentInvoice.weight)
        $('#data').text(currentInvoice.extra_data)

         $('#nds-name').text(currentInvoice.application.crops.name.nds.name)
         $('#nds-number').text(currentInvoice.application.crops.name.nds.number)
         $('#nds-type').text(nds_type)

    }
</script>
