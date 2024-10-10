@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

<div id="invoice-cheque" class="py-4 col-12 {{ $classes ?? '' }}" style=" font-family: Times New Roman;">
        <div class="text-center">
            <img  height="300" src="{{ asset('/img/dashboard/gerb.png') }}">
        </div>
        <h2 style="text-align: center">O'zbekiston Respublikasi Qishloq xo'jaligi vazirligi huzuridagi Agrosanoat majmui ustidan nazorat qilish<br>
            Inspeksiyasi  qoshidagi “Qishloq xo‘jaligi mahsulotlari sifatini baholash markazi” davlat muassasasi</h2>

    <h1 class="text-center"><b>SIFAT SERTIFIKATI</b></h1>
    <h2 class="text-center">Reestr raqami: 01024001</h2>
    <h2 class="text-left">_____ filyali Paxta mahsulotlari sinov laboratoriyasi</h2>
    <h2 class="text-left">Berilgan sana : <span style="padding: 5px;" id="application-date"></span> yil</h2>
    <h2 class="text-center">{{$test->crops->name->name}} - {{$test->crops->name->kodtnved}}</h2>
    <h2 class="text-left">Ishlab chiqaruvchi: {{ $test->organization->name }}</h2>
    <h2 class="text-left">Ishlab chiqaruvchi manzili: {{ $test->organization->full_address }}</h2>

{{--    <h3 class="text-center"><b>TEXNIK  CHIGITNING  SIFATINI  ANIQLASH  NATIJALARI  BO‘YICHA<br>--}}
{{--            _______ - sonli  SINOV  BAYONNOMASI</b></h3>--}}
{{--    <div class="row">--}}
{{--        <div class="col-md-6">To'da raqami - {{$test->crops->party_number}}</div>--}}
{{--        <div class="col-md-6">Dublikat raqami - {{$test->crops->party2}}</div>--}}
{{--    </div>--}}
{{--    <h4 class="text-left">Ishlab chiqaruvchi (etkazib--}}
{{--        beruvchi) nomi va kodi:  &nbsp;&nbsp; <span style="text-decoration: underline"> {{$test->prepared->name}} - {{$test->prepared->kod}} </span>&nbsp;&nbsp; paxta tozalash korxonasi </h4>--}}
{{--    <h4 class="text-left"> Xaridor nomi:&nbsp;&nbsp; <span style="text-decoration: underline"> {{$test->client_data->client->name}} </span>&nbsp;&nbsp; yog‘-moy korxonasi </h4>--}}
{{--    <div class="row">--}}
{{--        <div class="col-md-6">Avtotransport/ vagon raqami:  {{$test->client_data->vagon_number}}</div>--}}
{{--        <div class="col-md-6">Yuk xati: {{ $test->client_data->yuk_xati }}</div>--}}
{{--    </div>--}}

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
    <div class="row">
        <div class="col-sm-4" style="display:block; font-size: 20px">
                    <span style="padding: 5px; display: block">
                        {{ $user->lastname . ' ' . ($user->name) }}</span>

        </div>
        <div class="col-sm-4"></div>
        <div class="col-sm-4" style="display: flex; flex-direction: column; justify-content: end;">
            <div class="text-center"> {!! $qrCode !!}</div>
        </div>
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
