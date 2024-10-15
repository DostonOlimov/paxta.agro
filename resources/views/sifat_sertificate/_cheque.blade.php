@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

<div id="invoice-cheque" class="py-4 col-12 {{ $classes ?? '' }}" style=" font-family: Times New Roman;">
        <div class="text-center">
            <img  height="300" src="{{ asset('/img/dashboard/gerb.png') }}">
        </div>
        <h2 style="text-align: center">O’zbekiston Respublikasi Qishloq xo’jaligi vazirligi huzuridagi <br> Agrosanoat majmui ustidan nazorat qilish
            Inspeksiyasi  qoshidagi <br> “Qishloq xo‘jaligi mahsulotlari sifatini baholash markazi” <br> davlat muassasasi</h2>

    <h1 class="text-center"><b>SIFAT SERTIFIKATI</b></h1>
    <h2 class="text-center">Reestr raqami: </h2>
    <h2 class="text-left"><b>Sertifikatlanuvchi mahsulot nomi :</b> {{$test->crops->name->name}} </h2>
    <h2 class="text-left"><b>KOD TN VED :</b> {{$test->crops->name->kodtnved}}</h2>

    <h2 class="text-left"><b>Berilgan sana :</b> {{ $formattedDate }} - yil</h2>

    <h2 class="text-left"><b>Ishlab chiqaruvchi (yetkazib beruvchi)  nomi : </b> {{ $test->organization->name }}</h2>
    <h2 class="text-left"><b>Ishlab chiqaruvchi (yetkazib beruvchi) manzili : </b>  {{ $test->organization->full_address }}</h2>

    <h2 class="text-left" style="display: inline;"><b>Texnik chigit to'da raqami : </b> {{$test->crops->party_number}}</h2>


    <h2 class="text-left"> <b>Xaridor (yog‘-moy korxonasi) nomi:&nbsp;</b>&nbsp;  {{$test->client_data->client->name}} &nbsp;  </h2>
    <div class="row">
        <div class="col-md-6" style="font-size: 28px"><b>Avtotransport/ vagon raqami: </b> {{$test->client_data->vagon_number}}</div>
        <div class="col-md-6" style="font-size: 28px"><b>Yuk xati:</b> {{ $test->client_data->yuk_xati }}</div>
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
            <th colspan="2">O’zDSt 597 Nuqsonli chigitning massaviy ulushi (%)</th>
            <th colspan="2">O’zDSt 599 Mineral va organik aralashmalarning massaviy ulushi (%)</th>
            <th colspan="2">O’zDSt 601 Tukdorlikning massaviy  ulushi (%)</th>
            <th colspan="2">O’zDSt 600 Namlikning massaviy  ulushi (%)</th>
        </tr>
        <tr>
            <th>Me'yorda</th>
            <th>Amalda</th>
            <th>Me'yor</th>
            <th>Amalda</th>
            <th>Me'yor</th>
            <th>Amalda</th>
            <th>Me'yor</th>
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
    <h3 class="small_notes"> To‘da yuqoridagi ko‘rsatkichlari bo‘yicha O’z DSt 596 standartining 4.1, 4.2 bandlariga muvofiq.</h3>
    <div class="row">
        <div class="col-sm-6">
            <span style="padding: 5px; display: block;"><b>Ijrochi :</b>
            {{ optional($test->user->zavod)->region->name }} filialining<br>
            {{ optional(optional($test->user->zavod)->chigit_laboratory)->name }}<br>
            mudiri-guruh rahbari :
            {{ $test->user->lastname . ' ' . ($test->user->name) }}

        </span>
        </div>
        <div class="col-sm-2"></div>
<!-- {{--        <div class="col-sm-4" style="display: flex; flex-direction: column; justify-content: end;">--}}
{{--            <div class="text-center"> {!! $qrCode !!}</div>--}}
{{--        </div>--}} -->
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
