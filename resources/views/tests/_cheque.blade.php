<div id="invoice-cheque" class="py-4 col-12" style=" font-family: Times New Roman;">
    <h3 class="text-center">Oʼzbekiston Respublikasi Qishloq xo'jaligi vazirligi huzuridagi Аgrosanoat majmui ustidan <br>
        nazorat qilish Inspektsiya qoshidagi “Qishloq xo'jaligi mahsulotlarini baholash markazi” davlat muassasining
        paxta mahsulotlarini sertifikatlashtirish organi<br>
    </h3>
    <h3 class="text-right">
        <b> «TАSDIQLАYMАN» </b><br>
        Paxta mahsulotlarini<br>
        sertifikatlashtirish organi boshligʼi<br>
       <span id="director-name"></span><br>
        <span id="application-date"></span> yil
        <div class="text-center"> {!! $qrCode !!}</div>
    </h3>
    <b>
        <h3 class="text-center">Paxta tolasini sifatini laboratoriyada aniqlash bo'yicha<br>
            <b><h1 class="text-center">Sinov dasturi</h1>&nbsp{{ optional($decision->application->decision)->number}}-sonli</b>
        </h3>

        <h3 class="text-left">
           Sinov laboratoriyasi nomi : {{ optional($decision->application->decision)->laboratory->name}}
        </h3>
        <h3 class="text-left">
            Sinov laboratoriyasi manzili : {{ optional($decision->application->decision)->laboratory->full_address}}
        </h3>
        <h3 class="text-left">
            Laboratoriya tahlillaridan o'tkaziladigan mahsulot turi : {{ optional($decision->application->crops)->name->name}}
        </h3>
        <h3 class="text-left">
            Texnikaviy shartlar me'yoriy hujjati : {{ $nds_type }}
        </h3>
        <h3 class="text-center">
            Quyidagi sifat ko'rsatkichlari bo'yicha laboratoriya sinovlari o'tkazilsin:
        </h3>

    <table class="table table-bordered align-middle first-table">
        <tr>
            <td style="font-size: 20px !important;"  >T\r</td>
            <td style="font-size: 20px !important;" >Ko'rsatkichlar nomlanishi</td>
            <td style="font-size: 20px !important;" >Me'yoriy hujjatlar</td>
        </tr>
        @foreach($indicators as $k => $indicator)
            <tr>
                <td style="font-size: 20px !important;" >{{$loop->iteration}}</td>
                <td style="font-size: 20px !important;" >{{$indicator->name}}</td>
                <td style="font-size: 20px !important;" >{!! nl2br($indicator->nd_name) !!}</td>
            </tr>
        @endforeach
    </table>
    <h4>
        *3,4,5 navlar uchun to'dadagi na'munalarni 10% gacha sinaladi.
    </h4>

</div>


