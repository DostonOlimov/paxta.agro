@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

<div id="invoice-cheque" class="py-4 col-12 {{ $classes ?? '' }}" style=" font-family: Times New Roman;">

        <h3 style="text-align: center">O'zbekiston Respublikasi Qishloq xo'jaligi vazirligi huzuridagi Agrosanoat majmui ustidan nazorat qilish<br>
            Inspeksiyasi  qoshidagi “Qishloq xo‘jaligi mahsulotlari sifatini baholash markazi” davlat muassasasi</h3>
    <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-4" style="display: flex; flex-direction: column; justify-content: end;">
            <div class="text-center"> {!! $qrCode !!}</div>
        </div>
        <div class="col-sm-4" style="display:block;">
            <h3 class="text-center" style="font-size: 24px">
                 «T A S D I Q L A Y M A N»<br>
                    {{$user->state->name}} filialining
                    paxta  tozalash  korxonasi huzuridagi chigit sinov
                    laboratoriyasi mudiri-guruh rahbari
                    <span style="padding: 5px; display: block">
                            {{ $user->lastname . '. ' . substr($user->name, 0, 1) }}</span>
                    <span style="padding: 5px;" id="application-date"></span> yil
            </h3>
        </div>
    </div>
    <h3 class="text-center"><b>TEXNIK  CHIGITNING  SIFATINI  ANIQLASH  NATIJALARI  BO‘YICHA<br>
            _______ - sonli  SINOV  BAYONNOMASI</b></h3>
    <div class="row">
        <div class="col-md-6">To'da raqami - {{$test->crops->party_number}}</div>
        <div class="col-md-6">Dublikat raqami - {{$test->crops->party2}}</div>
    </div>
    <h4 class="text-left">Ishlab chiqaruvchi (etkazib
        beruvchi) nomi va kodi:  &nbsp;&nbsp; <span style="text-decoration: underline"> {{$test->prepared->name}} - {{$test->prepared->kod}} </span>&nbsp;&nbsp; paxta tozalash korxonasi </h4>
    <h4 class="text-left"> Xaridor nomi:&nbsp;&nbsp; <span style="text-decoration: underline"> {{$test->client_data->client->name}} </span>&nbsp;&nbsp; yog‘-moy korxonasi </h4>
    <div class="row">
        <div class="col-md-6">Avtotransport/ vagon raqami:  {{$test->client_data->vagon_number}}</div>
        <div class="col-md-6">Yuk xati: {{ $test->client_data->yuk_xati }}</div>
    </div>

    <h3 class="text-center"> ISHLAB CHIQARUVCHI (ETKAZIB BERUVCHI) NING MA’LUMOTLARI</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Texnik chigit turi (jinlangan/linterlangan)</th>
                <th>Seleksiya navi</th>
                <th>Netto massasi (kg)</th>
                <th>Konditsion massasi (kg)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $test->crops->name->name }}</td>
                <td>{{ optional($test->crops->selection)->name }}</td>
                <td>{{ $test->crops->amount }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <h3 class="text-center"> IJROCHINING MA’LUMOTLARI</h3>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Navi/ sinfi</th>
            <th colspan="2">O’zDSt 597:2008 Nuqsonli chigitning massaviy ulushi (%)</th>
            <th colspan="2">O’zDSt 599:2008 Mineral va organik aralashmalarning massaviy ulushi (%)</th>
            <th colspan="2">O’zDSt 601:2008 Tukdorlikning massaviy  ulushi (%)</th>
            <th colspan="2">O’zDSt 600:2008 Namlikning massaviy  ulushi (%)</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td></td>
            <td>1.5</td>
            <td>{{ optional($test->chigit_result()->where('indicator_id','=',9)->first())->value }}</td>
            <td> - </td>
            <td>{{ optional($test->chigit_result()->where('indicator_id','=',10)->first())->value }}</td>
            <td>7.0</td>
            <td>{{ optional($test->chigit_result()->where('indicator_id','=',12)->first())->value }}</td>
            <td>8.0</td>
            <td>{{ optional($test->chigit_result()->where('indicator_id','=',11)->first())->value }}</td>
        </tr>
        </tbody>
    </table>
    <h4 class="text-left"> Izoh: To‘da quyidagi ko‘rsatkichlari bo‘yicha O’z DSt 596 talablariga muvofiq keladi.</h4>

    </div>

<script>
    function printCheque() {
        $('#invoice-cheque').print({
            NoPrintSelector: '.no-print',
            title: '',
        })
    }
</script>
