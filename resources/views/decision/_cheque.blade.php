<div id="invoice-cheque" class="py-4 col-12 {{$classes ?? ''}}" style=" font-family: Times New Roman;">
    <h3 class="text-center">Oʼzbekiston Respublikasi Qishloq xo'jaligi vazirligi huzuridagi Аgrosanoat majmui ustidan <br>
        nazorat qilish Inspektsiya qoshidagi “Qishloq xo'jaligi mahsulotlarini baholash markazi” davlat muassasining
        paxta mahsulotlarini sertifikatlashtirish organi<br></h3>
    <h3 class="text-right">
       <b> «TАSDIQLАYMАN» </b><br>
        Paxta mahsulotlarini<br>
         sertifikatlashtirish organi boshligʼi<br>
        <span id="director-name"></span><br>
        <span id="application-date"></span> yil
        <div class="text-center"> {!! $qrCode !!}</div>

    </h3>
    <b>
    <h3 class="text-center">Sertifikatlashtirishni oʼtkazish uchun berilgan ariza boʼyicha<br>
        <span id="application-year"></span> &nbsp yil &nbsp “<span id="application-month"></span>”&nbsp <span id="application-day"></span> &nbsp<span id="application-id"></span>-sonli</h3>
    <h1 class="text-center">QAROR</h1></b>

                <h3 class="text-center mb-0">
                    <span id="application-organization"></span><br>
                </h3>

                <h3 class="text-center">
                    <span id="crop-name"></span> &nbsp kod tnved : <span id="crop-tnved"></span>
                </h3>

                <h3 class="text-left">
              Sertifikatlashtirish uchun berilgan arizasini koʼrib chiqib quyidagilarni ma'lum qilamiz:
                </h3>

                <h3 class=" text-left">
                    1. Sertifikatlashtirish ishlari _____7______ - sxemasi bo‘yicha o‘tkaziladi.
                </h3>

                <h3 class="text-left">
                    2. Sertifikatlashtirish uchun sinovlar  <span id="laboratory-name"></span>da (akkreditatsiya guvohnomasi
                    <span id="laboratory-certificate"></span>, manzil:      <span id="laboratory-address"></span> ) amalga oshiriladi.
                </h3>

                <h3 class=" text-left">
                    3. Sertifikatlashtirish &nbsp;<span id="nds-type"></span>&nbsp;  <span id="nds-number"></span>&nbsp; <span id="nds-name"></span>
                     talablariga muvofiq amalga oshiriladi.
                </h3>

                <h3 class=" text-left">
                    4. Ishlab chiqarishni tekshirish: Sertifikatlash sxemasida nazarda tutilmagan.
                </h3>

                <h3 class=" text-left">
                    5. To‘lov turi: Shartnoma asosida.
                </h3>

                <h3 class=" text-left">
                    6. Sertifikatlashtirish uchun talab etiladi: To'daga tegishli barcha ma'lumotlar.
                </h3>

</div>

<script>
    function printCheque() {
        $('#invoice-cheque').print({
            NoPrintSelector: '.no-print',
            title: '',
        })
    }
</script>
