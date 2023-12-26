<div id="invoice-cheque" class="py-4 col-12 {{$classes ?? ''}}">
    <h4 class="text-center">Oʼzbekiston Respublikasi Qishloq xo'jaligi vazirligi huzuridagi Аgrosanoat majmui ustidan <br>
        nazorat qilish Inspektsiya qoshidagi “Qishloq xo'jaligi mahsulotlarini baholash markazi” davlat muassasining
        paxta mahsulotlarini sertifikatlashtirish organi<br></h4>
    <h4 class="text-right">
       <b> «TАSDIQLАYMАN» </b><br>
        Paxta mahsulotlarini<br>
         sertifikatlashtirish organi boshligʼi<br>
        _________<span>Nurullayev M.</span><br>
        <span id="application-date"></span> yil

    </h4>
    <b>
    <h4 class="text-center">Sertifikatlashtirishni oʼtkazish uchun berilgan ariza boʼyicha<br>
        <span id="application-year"></span> &nbsp yil &nbsp “<span id="application-month"></span>”&nbsp <span id="application-day"></span> &nbsp<span id="application-id"></span>-sonli</h4>
    <h1 class="text-center">QAROR</h1></b>
    <table class="table align-left">
        <tr>
            <td class="align-left">
                <h3 class="text-center mb-0">
                    <span id="application-organization"></span><br>
                </h3>
            </td>
        </tr>
        <tr>
            <td  class="text-center" class="align-left">
                <h3 class="mb-0">
                    <span id="crop-name"></span> &nbsp kod tnved : <span id="crop-tnved"></span>
                </h3>
            </td>
        </tr>
        <tr>
            <td class="align-left">
                <h4 class="text-left">
              Sertifikatlashtirish uchun berilgan arizasini koʼrib chiqib quyidagilarni ma'lum qilamiz:
                </h4>
            </td>
        </tr>
        <tr>
            <td class="align-left">
                <h4 class=" text-left">
                    1. Sertifikatlashtirish ishlari _____7______ - sxemasi bo‘yicha o‘tkaziladi.</h4>
            </td>
        </tr>
        <tr>
            <td class="align-left">
                <h4 class="text-left">
                    2. Sertifikatlashtirish uchun sinovlar  <span id="laboratory-name"></span>da (akkreditatsiya guvohnomasi
                    <span id="laboratory-certificate"></span>, manzil:      <span id="laboratory-address"></span> ) amalga oshiriladi.
                </h4>
            </td>
        </tr>
        <tr>
            <td class="align-left">
                <h4 class=" text-left">
                    3. Sertifikatlashtirish &nbsp;<span id="nds-type"></span>&nbsp;  <span id="nds-number"></span>&nbsp; <span id="nds-name"></span>
                     talablariga muvofiq amalga oshiriladi.
                </h4>
            </td>
        </tr>
        <tr>
            <td class="align-left">
                <h4 class=" text-left">
                    4. Ishlab chiqarishni tekshirish: Sertifikatlash sxemasida nazarda tutilmagan.
                </h4>
            </td>
        </tr>
        <tr>
            <td class="align-left">
                <h4 class=" text-left">
                    5. To‘lov turi: Shartnoma asosida.
                </h4>
            </td>
        </tr>
        <tr>
            <td class="align-left">
                <h4 class=" text-left">
                    6. Sertifikatlashtirish uchun talab etiladi: To'daga tegishli barcha ma'lumotlar.
                </h4>
            </td>
        </tr>
    </table>
</div>

<script>
    function printCheque() {
        $('#invoice-cheque').print({
            NoPrintSelector: '.no-print',
            title: '',
        })
    }

    // function fillCheque() {
    //     $('#director-name').text((currentInvoice.director.name.charAt(0)).concat(".",currentInvoice.director.display_name.charAt(0),".",currentInvoice.director.lastname))
    //     $('#application-date').text(moment(currentInvoice.date).format('DD.MM.YYYY'))
    //     $('#application-year').text(moment(currentInvoice.date).format('YYYY'))
    //     $('#application-month').text(moment(currentInvoice.date).format('MM'))
    //     $('#application-day').text(moment(currentInvoice.date).format('DD'))
    //     $('#application-id').text(currentInvoice.number)
    //     $('#application-organization').text(currentInvoice.application.organization.name)
    //     $('#crop-name').text(currentInvoice.application.crops.name.name)
    //
    //     $('#laboratory-address').text(currentInvoice.laboratory.address)
    //     $('#laboratory-certificate').text(currentInvoice.laboratory.certificate)
    //     $('#laboratory-name').text(currentInvoice.laboratory.name)
    //
    //     $('#nds-name').text(currentInvoice.application.crops.name.nds.name)
    //     $('#nds-number').text(currentInvoice.application.crops.name.nds.number)
    //     $('#nds-type').text(nds_type)
    //
    // }
</script>
