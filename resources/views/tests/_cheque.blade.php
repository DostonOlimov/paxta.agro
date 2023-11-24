<div id="invoice-cheque" class="py-4 col-12 {{$classes ?? ''}}">
    <h4 class="text-center">Oʼzbekiston Respublikasi Qishloq xo'jaligi vazirligi huzuridagi Аgrosanoat majmui ustidan <br>
        nazorat qilish Inspektsiya qoshidagi “Qishloq xo'jaligi mahsulotlarini baholash markazi” davlat muassasining
        paxta mahsulotlarini sertifikatlashtirish organi<br></h4>
    <h4 class="text-right">
        <b> «TАSDIQLАYMАN» </b><br>
        Paxta mahsulotlarini<br>
        sertifikatlashtirish organi boshligʼi<br>
        _________<span id="director-name"></span><br>
        <span id="application-date"></span> yil
    </h4>
    <b>
        <h4 class="text-center">Paxta tolasini sifatini laboratoriyada aniqlash bo'yicha<br>
            <b><h1 class="text-center">Sinov dasturi</h1>&nbsp{{ optional($decision->application->decision)->number}}-sonli</b></h4>
    <div>
        <h4 class="text-left">
           Sinov laboratoriyasi nomi : {{ optional($decision->application->decision)->laboratory->name}}
        </h4>
        <h4 class="text-left">
            Sinov laboratoriyasi manzili : {{ optional($decision->application->decision)->laboratory->full_address}}
        </h4>
        <h4 class="text-left">
            Laboratoriya tahlillaridan o'tkaziladigan mahsulot turi : {{ optional($decision->application->crops)->name->name}}
        </h4>
        <h4 class="text-left">
            Texnikaviy shartlar me'yoriy hujjati : {{ $nds_type }}
        </h4>
    </div>
            <div>
                <h4 class="text-center">
                    Quyidagi sifat ko'rsatkichlari bo'yicha laboratoriya sinovlari o'tkazilsin:
                </h4>
            </div>

    <table class="table table-bordered align-middle">
        <tr>
            <th>T\r</th>
            <th>Ko'rsatkichlar nomlanishi</th>
            <th>Me'yoriy hujjatlar</th>
        </th>
        @foreach($indicators as $k => $indicator)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$indicator->name}}</td>
                <td>{!! nl2br($indicator->nd_name) !!}</td>
            </tr>
        @endforeach
    </table>
    <div>
        *3,4,5 navlar uchun to'dadagi na'munalarni 10% gacha sinaladi.
    </div>

</div>
<script>
    function printCheque() {
        $('#invoice-cheque').print({
            NoPrintSelector: '.no-print',
            title: '',
        })
    }
</script>

