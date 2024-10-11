@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

<div id="invoice-cheque" class="py-4 col-12 {{ $classes ?? '' }}" style=" font-family: Times New Roman;">
        <div class="text-center">
            <img  height="300" src="{{ asset('/img/dashboard/gerb.png') }}">
        </div>
        <h2 style="text-align: center">O’zbekiston Respublikasi Qishloq xo’jaligi vazirligi huzuridagi Agrosanoat majmui ustidan nazorat qilish<br>
            Inspeksiyasi  qoshidagi “Qishloq xo‘jaligi mahsulotlari sifatini baholash markazi” davlat muassasasi</h2>

    <h1 class="text-center"><b>SIFAT SERTIFIKATI</b></h1>
    <h2 class="text-center">Reestr raqami: 01024001</h2>
    <h2 class="text-center">{{$test->crops->name->name}} - {{$test->crops->name->kodtnved}}</h2>

    <h2 class="text-left">Berilgan sana : <span style="padding: 5px;" id="application-date"></span> yil</h2>

    <h2 class="text-left">Ishlab chiqaruvchi: {{ $test->organization->name }}</h2>
    <h2 class="text-left">Ishlab chiqaruvchi manzili: {{ $test->organization->full_address }}</h2>

    <div class="row">
        <div class="col-md-6" style="font-size: 28px">To'da raqami - {{$test->crops->party_number}}</div>
        <div class="col-md-6" style="font-size: 28px">Dublikat raqami - {{$test->crops->party2}}</div>
    </div>
    <h2 class="text-left">Ishlab chiqaruvchi (etkazib
        beruvchi) nomi va kodi:  &nbsp;&nbsp; <span style="text-decoration: underline"> {{$test->prepared->name}} - {{$test->prepared->kod}} </span>&nbsp;&nbsp; paxta tozalash korxonasi </h2>
    <h2 class="text-left"> Xaridor nomi:&nbsp;&nbsp; <span style="text-decoration: underline"> {{$test->client_data->client->name}} </span>&nbsp;&nbsp; yog‘-moy korxonasi </h2>
    <div class="row">
        <div class="col-md-6" style="font-size: 28px">Avtotransport/ vagon raqami:  {{$test->client_data->vagon_number}}</div>
        <div class="col-md-6" style="font-size: 28px">Yuk xati: {{ $test->client_data->yuk_xati }}</div>
    </div>

    <h3 class="text-center"> ISHLAB CHIQARUVCHI (ETKAZIB BERUVCHI) NING MA’LUMOTLARI</h3>
    <table class="table table-bordered" >
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
                <td>{{ round ($test->crops->amount * (100 - $namlik - $zararkunanda) / (100 - 10 - 0.5)) }}</td>
            </tr>
        </tbody>
    </table>
    <h3 class="text-center"> IJROCHINING MA’LUMOTLARI</h3>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th rowspan="2">Navi/ sinfi</th>
            <th colspan="2">O’zDSt 597:2008 Nuqsonli chigitning massaviy ulushi (%)</th>
            <th colspan="2">O’zDSt 599:2008 Mineral va organik aralashmalarning massaviy ulushi (%)</th>
            <th colspan="2">O’zDSt 601:2008 Tukdorlikning massaviy  ulushi (%)</th>
            <th colspan="2">O’zDSt 600:2008 Namlikning massaviy  ulushi (%)</th>
        </tr>
        <tr>
            <th>Me'yorda</th>
            <th>Amalda</th>
            <th>Me'yorda</th>
            <th>Amalda</th>
            <th>Me'yorda</th>
            <th>Amalda</th>
            <th>Me'yorda</th>
            <th>Amalda</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ $tip->nav }} / {{ $tip->sinf }}</td>
            <td>{{ number_format( $tip->nuqsondorlik, 1, '.', '.') }}</td>
            <td>{{ number_format( $nuqsondorlik, 1, '.', '.') }}</td>
            <td> - </td>
            <td>{{ number_format( $zararkunanda, 1, '.', '.') }}</td>
            <td>{{ number_format( $tip->tukdorlik, 1, '.', '.') }}</td>
            <td>{{ number_format( $tukdorlik, 1, '.', '.') }}</td>
            <td>{{ number_format( $tip->namlik, 1, '.', '.') }}</td>
            <td>{{ number_format( $namlik, 1, '.', '.') }}</td>
        </tr>
        </tbody>
    </table>
    <h3> Izoh: To‘da quyidagi ko‘rsatkichlari bo‘yicha O’z DSt 596 talablariga muvofiq keladi.</h3>
    <div class="row">
        <div class="col-sm-6" style="display:block; font-size: 20px">
                    <span style="padding: 5px; display: block">
                        {{ optional($test->user->zavod)->region->name }}<br>
                        {{ optional(optional($test->user->zavod)->chigit_laboratory)->name }}<br>
                        guruh rahbari
                        {{ $test->user->lastname . ' ' . ($test->user->name) }}</span>

        </div>
        <div class="col-sm-2"></div>
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
