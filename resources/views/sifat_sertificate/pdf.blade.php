@extends('layouts.pdf')
@section('styles')
<style>
    .invoice-cheque {
        width: 100% !important;
        margin: 0 auto;
        font-size: 16px;
    }

    .small_notes{
        font-size: 12px;
        display: block;
        padding: 0 !important;
        margin: 0 !important;
    }
    .header__title {
        font-size: 18px;
        text-align: center;
        margin-top: 10px;
        text-transform: uppercase;
    }

    .header__intro {
        display: flex;
        justify-content: center;
        margin: 0 auto;
        text-align: center;
        font-size: 16px;
        max-width: 90%;
        line-height: 1.2;
    }
    .header__tasdiqlayman h2 {
        font-size: 16px;
        font-weight: bold;
    }

    .main__intro {
        display: flex;
        justify-content: center;
        margin: 0 auto;
        text-align: left;
        font-size: 16px;
        max-width: 100%;
        line-height: 1.2;
    }

    h1 {
        line-height: 1;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
    }

    h2 {
        font-weight: normal;
        flex: 1;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 5px 0;
    }

    table th,
    table td {
        border: 1px solid black;
        padding: 3px;
        text-align: center;
    }

    table th {
        font-weight: bold;
    }

    table td {
        font-size: 14px;
    }

    .container {
        display: flex;
        justify-content: center; /* Horizontal centering */
        align-items: center;     /* Vertical centering */
        height: 100vh;           /* Full viewport height or adjust accordingly */
    }

    img {
        max-width: 100%;         /* Optional: To make sure the image is responsive */
        height: auto;
    }
    .row {
        display: flex;
        justify-content: space-between; /* Ensure even spacing between columns */
        align-items: flex-start; /* Align the content at the top */
        margin-bottom: 20px; /* Add space between rows */
    }

    .col-sm-6, .col-sm-2, .col-sm-4 {
        padding: 10px; /* Add padding to columns */
    }

    .col-sm-6 {
        font-size: 16px;
        line-height: 1.5; /* Ensure good readability */
    }

    .col-sm-4 {
        display: flex;
        flex-direction: column;
        justify-content: flex-end; /* Align content at the bottom */
        align-items: center; /* Center the QR code */
        padding: 10px;
    }

    .text-center img {
        max-width: 100px; /* Restrict QR code width */
        margin-top: auto; /* Push the QR code to the bottom of the div */
    }

    @media (max-width: 768px) {
        .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
            text-align: center; /* Align text to center on small screens */
        }
    }
    /* QR code styling */
    /* .qr-code {
        text-align: center;
        margin: 20px 0;
    }

    .signature-section {
        display: flex;
        justify-content: space-between;
        margin-top: 40px;
    }

    .signature-section h2 {
        font-size: 12px;
        font-weight: bold;
    } */

    @media print {
        .invoice-cheque {
            width: 100%;
            border: none;
            margin: 0;
            padding: 0;
        }

        .header__tasdiqlayman,
        .header__title {
            margin: 0;
        }

        table th,
        table td {
            padding: 5px;
        }
    }
</style>
@endsection
@section('content')
    @php
        use SimpleSoftwareIO\QrCode\Facades\QrCode;
    @endphp
<body>
<div id="invoice-cheque" class="py-4 col-12 invoice-cheque ">
    <div class="container">
        <img  height="200" src="{{ asset('/img/dashboard/gerb.png') }}" alt="image">
    </div>
    <h2 class="header__title">O’zbekiston Respublikasi Qishloq xo’jaligi vazirligi huzuridagi Agrosanoat majmui ustidan nazorat qilish<br>
        Inspeksiyasi  qoshidagi “Qishloq xo‘jaligi mahsulotlari sifatini baholash markazi” davlat muassasasi</h2>

    <h1  class="header__intro" style="font-weight: bold;">SIFAT SERTIFIKATI</h1>
    <h2 class="header__intro" style="font-weight: bold;">Reestr raqami: 01024001</h2>
    <h2 class="main__intro">Sertifikatlanuvchi mahsulot nom(kod tnved)i : {{$test->crops->name->name}} - {{$test->crops->name->kodtnved}}</h2>

    <h2 class="main__intro">Berilgan sana : {{ $formattedDate }}</h2>

    <h2 class="main__intro text-left">Ishlab chiqaruvchi: {{ $test->organization->name }}</h2>
    <h2 class="main__intro text-left">Ishlab chiqaruvchi manzili: {{ $test->organization->full_address }}</h2>

    <div style="display: flex !important; justify-content: space-between !important;">
        <h2 class="header__intro" style="display: inline;">To'da raqami - {{$test->crops->party_number}}</h2>
        <h2 class="header__intro" style="display: inline;">Dublikat raqami - {{$test->crops->party2}}</h2>
    </div>
    <h2 class="main__intro text-left">Ishlab chiqaruvchi (etkazib
        beruvchi) nomi va kodi:  &nbsp;&nbsp; {{$test->prepared->name}} - {{$test->prepared->kod}}&nbsp;&nbsp;&nbsp;&nbsp; paxta tozalash korxonasi </h2>
    <h2 class="main__intro text-left"> Xaridor nomi:&nbsp;&nbsp;  {{$test->client_data->client->name}} &nbsp; yog‘-moy korxonasi </h2>
    <div style="display: flex !important;  justify-content: space-between !important;">
        <h2 class="header__intro" style="display: inline;">Avtotransport/ vagon raqami:  {{$test->client_data->vagon_number}}</h2>
        <h2 class="header__intro" style="display: inline;">Yuk xati: {{ $test->client_data->yuk_xati }}</h2>
    </div>

    <h3 class="header__intro text-center" style="margin-top: 15px;"> ISHLAB CHIQARUVCHI (ETKAZIB BERUVCHI) NING MA’LUMOTLARI</h3>
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
    <h3 class="header__intro text-center" style="margin-top: 15px;"> IJROCHINING MA’LUMOTLARI</h3>
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
    <h3 class="small_notes"> Izoh: To‘da quyidagi ko‘rsatkichlari bo‘yicha O’z DSt 596 talablariga muvofiq keladi.</h3>
    <div class="row">
        <div class="col-sm-6">
        <span style="padding: 5px; display: block;">
            {{ optional($test->user->zavod)->region->name }}<br>
            {{ optional(optional($test->user->zavod)->chigit_laboratory)->name }}<br>
            guruh rahbari
            {{ $test->user->lastname . ' ' . ($test->user->name) }}
        </span>
        </div>
        <div class="col-sm-4">
            <div class="text-center">
                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
            </div>
        </div>
    </div>
</div>

</body>
@endsection
