
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
            <span> <b>Ariza beruvchining nomi:</b>{{ $test->test_program->application->organization->name }}.</span>
        </div>
        <div style="width: 25%; display: inline-block; text-align: right">
            <span><b>Hosil yili:</b> {{ $test->test_program->application->crops->year }} y.</span>
        </div>
    </div>
    <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
        <span><b>Ishlab chiqaruvchi nomi va kodi:</b>  {{ $test->test_program->application->prepared->name . ' - ' . str_pad($test->test_program->application->prepared->kod, 3, '0', STR_PAD_LEFT) }}</span>
    </div>
    <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
        <div style="width: 69%; display: inline-block;">
            <span> <b>Namuna olish dalolatnomasi raqami <span style="font-family: 'DejaVu Serif'"> № </span> </b>{{ $test->number }}.</span>
        </div>
        <div style="width: 30%; display: inline-block; text-align: left">
            <span> {{ $formattedDate2}} y.</span>
        </div>
    </div>
    @if(session('crop') != \App\Models\CropsName::CROP_TYPE_4)
        <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
            <div style="width: 49%; display: inline-block;">
                <span> <b>Seleksiya nomi:  </b>{{ $test->selection->name  }}.</span>
            </div>
            <div style="width: 50%; display: inline-block;">
                <span> <b>Laboratoriya kodi:  </b> {{  str_pad($test->test_program->application->decision->laboratory->kod, 2, '0', STR_PAD_LEFT) }} </span>
            </div>
        </div>
    @else
        <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
            <div style="width: 99%; display: inline-block;">
                <span> <b>Me'yoriy hujjati:  </b>O'zMSt 456:2024 "Paxta momig'i.Texnikaviy shartlari"</span>
            </div>
        </div>
    @endif


        <span style="padding: 10px 0;">Sinov natijasi:</span>
    @if(session('crop') != \App\Models\CropsName::CROP_TYPE_4)
        <table class="table table-border " style="border: 1px solid black ;text-align: center;font-size: 18px;">
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
                <td>{{  $item->amount ? $item->amount - $item->count * $test->tara : 0 }}</td>
                <td>{{ $item->sort }}</td>
                <td>{{ optional(\App\Models\CropsGeneration::where('kod','=',$item->class)->first())->name  }}</td>
                <td>{{  number_format($item->dalolatnoma->laboratory_result->fiblength/100, 2, '.', '.') }} </td>
                <td>{{  optional($test->laboratory_result)->tip->staple }}</td>

                <td>{{ round($item->mic,1) }}</td>
                <td>{{ number_format($item->strength, 1, '.', '.') }}</td>
                <td>{{ number_format($item->dalolatnoma->laboratory_result->uniform, 1, '.', '.') }}</td>
            </tr>
            @endforeach
            @endif
        </table>
    @else
        <table class="table table-border " style="border: 1px solid black ;text-align: center;font-size: 18px;">
            <tr>
                {{-- <th">T\r</th> --}}
                <th rowspan="2"> <span style="font-family: 'DejaVu Serif'"> № </span></th>
                <th colspan="2"> Korxona</th>
                <th rowspan="2"> To‘dadagi toylar soni (dona)</th>
                <th rowspan="2"> Netto massasi (kg)</th>
                <th rowspan="2"> Nav</th>
                <th rowspan="2"> Sinf</th>
                <th colspan="2"> O'zMSt 384:2024 <br> Tip(mm) </th>
            </tr>
            <tr>
                <th rowspan="1">kodi</th>
                <th rowspan="1"> to‘da raqami</th>
                <th rowspan="1"> A <br>7-8 va unda yuqori</th>
                <th rowspan="1"> B <br>6-7 va undan kam</th>
            </tr>
            @if ($final_results)
                @foreach ($final_results as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{  str_pad($test->test_program->application->prepared->kod, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $test->test_program->application->crops->party_number}}</td>
                        <td>{{ $item->count }}</td>
                        <td>{{ $item->amount ? $item->amount - $item->count * $test->tara : 0 }}</td>
                        <td>{{ $item->sort }}</td>
                        <td>{{ optional(\App\Models\CropsGeneration::where('kod','=',$item->class)->where('crop_id',4)->first())->name  }}</td>
                        <td>@if($type == 1) 7-8 @else - @endif</td>
                        <td>@if($type == 2) 6-7 @else - @endif</td>
                    </tr>
                @endforeach
            @endif
        </table>
    @endif

        <div style="display: flex;font-size: 16px;">
            <p>
                Sinov natijalari, sinovdan o'tkazilgan namunalarga tegishlidir.
            </p>
        </div>
        <div class="comments-section">
            <div style=" border-bottom: 1px solid black; margin: 25px 0;">Izoh:</div>
            <div style=" border-bottom: 1px solid black; margin: 25px 0;"></div>
            <div style=" border-bottom: 1px solid black; margin: 25px 0;"></div>
        </div>
    <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px;font-size: 18px;">
        <div style="width: 49%; display: inline-block;">
            <span> <b>Paxta tolasi, paxta momig'ini tasniflash bo'yicha mutaxassis  </b></span>
        </div>
        <div style="width: 50%; display: inline-block;text-align: center">
            <span style="font-family: 'DejaVu Serif'">
                {{ optional($test->laboratory_final_results->klassiyor)->name }}
            </span>
        </div>
    </div>
    @if(session('crop',1) != \App\Models\CropsName::CROP_TYPE_4)
        <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
            <div style="width: 49%; display: inline-block;">
                <span> <b>Texnologik qurilmalar
                    operatori (HVI)  </b></span>
            </div>
            <div style="width: 50%; display: inline-block;text-align: center">
                <span style="font-family: 'DejaVu Serif'"> {{ optional($test->laboratory_final_results->operator)->name }}</span>
            </div>
        </div>
    @endif
</div>

