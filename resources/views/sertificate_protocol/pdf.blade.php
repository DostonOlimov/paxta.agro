@extends('layouts.pdf')
@section('styles')
<style>
    .invoice-cheque {
        width: 100% !important;
        margin: 0 auto;
        font-size: 16px;
        height: 100vh; /* Ensure full height of the page */
        overflow: hidden; /* Prevent content from spilling over */
    }

    .header__title {
        font-size: 16px;
        text-align: center;
        margin-top: 1px;
        text-transform: uppercase;
    }

    .header__intro {
        display: flex;
        justify-content: center;
        margin: 0 auto;
        text-align: center;
        font-size: 16px;
        max-width: 90%;
        line-height: 1.3;
    }

    .main__intro {
        display: flex;
        justify-content: center;
        margin: 0 auto;
        text-align: left;
        font-size: 16px;
        max-width: 100%;
        line-height: 1.6;
    }

    h1 {
        line-height: 1.6;
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
        justify-content: center;
        /* Horizontal centering */
        align-items: center;
        /* Vertical centering */
        height: 100vh;
        /* Full viewport height or adjust accordingly */
    }

    img {
        max-width: 100%;
        /* Optional: To make sure the image is responsive */
        height: 150px;
        padding-left: 125px;
    }

    .text-center img {
        max-width: 100px;
        /* Restrict QR code width */
        margin-top: auto;
        /* Push the QR code to the bottom of the div */
    }


    #invoice-cheque {
        position: relative;
        width: 100%;
        height: 100vh;
    }
    .background_image {
        position: absolute;
        top: 55%;
        left: 32%;
        transform: translate(-50%, -50%); /* Center the image */
        width: 550px;
        height: auto;
        opacity: 0.1; /* Adjust the opacity as needed */
        z-index: -1;
    }
    .content {
        position: relative;
        z-index: 1; /* Keeps content above the image */
    }

</style>
@endsection
@section('content')
@php
use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

<div id="invoice-cheque" class="py-4 col-12">
    <div>
        <h2 style="text-align: center; margin: 10px 0; font-size: 24px"><b>
                “QISHLOQ XO‘JALIGI MAHSULOTLARI SIFATINI BAHOLASH<br> MARKAZI” DAVLAT MUASSASASI
            </b>
        </h2>
    </div>
    <div style="width: 100%; display: flex; font-size: 18px; padding-top: 15px">
        <div style="width: 59%; display: inline-block;">
            <div style="width: 30%;padding-top:20px; text-align: right; display: inline-block;">
                @if(!isset($t))
                    <img src="data:image/png;base64,{{ $qrCode }}" style="height: 100px;" alt="QR Code"><br>
                @endif

                <span style="display: block; margin-top: 5px;margin-left: 120px;"> @if(isset($sert_number)) {{ substr($sert_number, 2) }} @endif</span>

            </div>
        </div>
        <div style="width: 40%; display: inline-block;text-align: right">
            <b>{{ $test->test_program->application->decision->laboratory->name }} boshlig‘i
                <span style="padding: 5px; display: block">
                        {{ $test->laboratory_final_results->director->lastname . '. ' . substr($test->laboratory_final_results->director->name, 0, 1) }}</span>
            </b>
        </div>
    </div>
    <h1 style="padding-top:10px" class="text-center">
        <b>
            <span style="font-size:24px;">  SINOV BAYONNOMASI <span style="font-family: 'DejaVu Serif'"> № </span>{{$test->laboratory_final_results->number}}</span>
        </b>
    </h1>

    <div style="width: 100%; display: flex; justify-content: space-between;font-size: 18px;">
        <div style="width: 40%; display: inline-block;">
            <p style=" text-decoration: underline;margin-bottom: 0">{{ $test->test_program->application->decision->laboratory->city->region->name }}.</p>
        </div>

        <div style="width: 50%; display: inline-block; text-align: right">
            <span style="text-decoration: underline">{{ $formattedDate }} y.</span>
        </div>
    </div>

    <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
        <div style="width: 74%; display: inline-block;">
            <b>Ariza beruvchining nomi:</b><span style="font-family: 'DejaVu Serif'">{{ $test->test_program->application->organization->name }}.</span>
        </div>
        <div style="width: 25%; display: inline-block; text-align: right">
            <span><b>Hosil yili:</b> {{ $test->test_program->application->crops->year }} y.</span>
        </div>
    </div>
    <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
        <b>Ishlab chiqaruvchi nomi va kodi:</b><span style="font-family: 'DejaVu Serif'">  {{ $test->test_program->application->prepared->name . ' - ' . str_pad($test->test_program->application->prepared->kod, 3, '0', STR_PAD_LEFT) }}</span>
    </div>
    <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
        <div style="width: 69%; display: inline-block;">
            <span> <b>Namuna olish dalolatnomasi raqami <span style="font-family: 'DejaVu Serif'"> № </span> </b>{{ $test->number }}.</span>
        </div>
        <div style="width: 30%; display: inline-block; text-align: left">
            <span> {{ $formattedDate2}} y.</span>
        </div>
    </div>
    <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
        <div style="width: 49%; display: inline-block;">
            <span> <b>Seleksiya nomi:  </b>{{ $test->selection->name  }}.</span>
        </div>
        <div style="width: 50%; display: inline-block;">
            <span> <b>Laboratoriya kodi:  </b> {{  str_pad($test->test_program->application->decision->laboratory->kod, 2, '0', STR_PAD_LEFT) }} </span>
        </div>
    </div>

    <span style="padding: 10px 0;">Sinov natijasi:</span>

    <table class="table table-border " style="border: 1px solid black ;text-align: center;font-size: 16px;">
        <tr>
            {{-- <th">T\r</th> --}}
            <th rowspan="2"> <span style="font-family: 'DejaVu Serif'"> № </span></th>
            <th colspan="2"> Korxona</th>
            <th rowspan="2"> To‘dadagi toylar soni (dona)</th>
            <th rowspan="2"> Netto massasi (kg)</th>
            <th rowspan="2"> Nav</th>
            <th rowspan="2"> Sinf</th>
            <th colspan="2"> Yuqori o‘rtacha uzunlik </th>
            <th rowspan="2"> Mikro-neyr</th>
            <th rowspan="2"> Solishtirma uzulish kuchi gf/tex</th>
            <th rowspan="2"> Uzunlik bo’yicha birxillik indeksi%</th>
        </tr>
        <tr>
            <th rowspan="1">kodi</th>
            <th rowspan="1"> to‘da raqami</th>
            <th rowspan="1"> Dyum</th>
            <th rowspan="1"> Kodi</th>
        </tr>
        @if ($final_results)
            @foreach ($final_results as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{  str_pad($test->test_program->application->prepared->kod, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $test->test_program->application->crops->party_number}}</td>
                    <td>{{ $item->count }}</td>
                    <td>{{ $item->amount }}</td>
                    <td>{{ $item->sort }}</td>
                    <td>{{ optional(\App\Models\CropsGeneration::where('kod','=',$item->class)->first())->name  }}</td>
                    <td>{{  round($item->staple,1) }}</td>
                    <td>{{  optional($test->laboratory_result)->tip->staple }}</td>

                    <td>{{ round($item->mic,1) }}</td>
                    <td>{{ number_format($item->strength, 1, '.', '.') }}</td>
                    <td>{{ number_format($item->dalolatnoma->laboratory_result->fiblength/100, 2, '.', '.') }}</td>
                </tr>
            @endforeach
        @endif
    </table>

    <div style="display: flex;font-size: 16px;">
        <p>
            Sinov natijalari, sinov o’tkazilgan namunalarga tegishlidir.
        </p>
    </div>
    <div class="comments-section">
        <div style=" border-bottom: 1px solid black; margin: 25px 0;">Izoh:</div>
        <div style=" border-bottom: 1px solid black; margin: 25px 0;"></div>
        <div style=" border-bottom: 1px solid black; margin: 25px 0;"></div>
    </div>
    <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px;font-size: 18px;">
        <div style="width: 49%; display: inline-block;">
            <span> <b>Paxta mahsuloti sifatini tasniflash
                bo‘yicha mutaxassis (klasser)  </b></span>
        </div>
        <div style="width: 50%; display: inline-block;text-align: center">
            <span>
                {{ optional($test->laboratory_final_results->klassiyor)->name }}
            </span>
        </div>
    </div>
    <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
        <div style="width: 49%; display: inline-block;">
            <span> <b>Texnologik qurilmalar
                operatori (HVI)  </b></span>
        </div>
        <div style="width: 50%; display: inline-block;text-align: center">
            <span> {{ optional($test->laboratory_final_results->operator)->name }}</span>
        </div>
    </div>
</div>

@endsection
