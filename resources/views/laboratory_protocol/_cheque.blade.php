@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp
<div id="invoice-cheque" class="py-4 col-12 {{ $classes ?? '' }}" style=" font-family: Times New Roman;">
    <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-4" style="display: flex; flex-direction: column; justify-content: end;">
            <div class="text-center"> {!! $qrCode !!}</div>
        </div>
        <div class="col-sm-4" style="display:block;">
            <span style="display: block" class="text-center"><b>PSK-2-06-F1 ilovasi</b></span>
            {{-- <span style="display: block" class="text-center"><b>1/1</b></span> --}}
            <h2 class="text-center">
                <b> «T A S D I Q L A Y M A N»<br>
                    Markaziy laboratoriya boshlig‘i
                    <span style="padding: 5px;display: block">
                        {{ $test->laboratory_final_results->director->lastname . '. ' . substr($test->laboratory_final_results->director->name, 0, 1) }}</span>
                    <span style="padding: 5px;" id="application-date"></span> yil
            </h2>
        </div>
    </div>

    <div>
        <h2 style="text-align: center; margin-top: 10px"><b>
                {{-- Oʼzbekiston Respublikasi Qishloq xo'jaligi vazirligi huzuridagi Аgrosanoat majmui ustidan
            nazorat qilish Inspektsiyasi qoshidagi<br> --}}
                “Qishloq xo'jaligi mahsulotlari sifatini baholash markazi” davlat muassasasining.<br>
                {{ $test->test_program->application->decision->laboratory->name }} <br>
                Davlat reestri raqami
                {{ $test->test_program->application->decision->laboratory->certificate . ' ' . $test->test_program->application->decision->laboratory->address }}
            </b>
        </h2>
        <div style="display: flex; justify-content: end; padding-right: 16%">
            <p>Laboratoriya kodi <span
                    style="text-decoration: underline;">{{ $test->test_program->application->decision->laboratory->kod }}</span>
            </p>
        </div>
    </div>

    <h1 style="padding-top: 30px" class="text-center"><b>PAXTA TOLASINING</b><br>
    </h1>
    <h1 class="text-center"><b>0{{ optional($test->laboratory_final_results)->number }} - SONLI SINOV BAYONNOMASI </b>
    </h1>

    @php
        use Carbon\Carbon;
        $formattedDate = Carbon::parse($test->laboratory_final_results->date)->format('d-m-Y');
        $dateDalolatnoma = Carbon::parse($test->date)->format('d-m-Y');
    @endphp

    <div>
        <div style="display: flex; justify-content: space-between; font-weight: 500;font-size: 20px;">
            <p>
                {{ $test->test_program->application->decision->laboratory->city->region->name }}.
            </p>
            <p>{{ $formattedDate }} y.</p>
        </div>
        <div style="display: flex; font-weight: 500;font-size: 20px; margin-bottom: 0; height: 25px;">
            <p>Yetkazib beruvchining nomi</p>

            <p style="height: 26px;margin-left: 30%; padding-bottom: 0px; border-bottom: solid 1px black">
                {{ $test->test_program->application->organization->name . ' ' . str_pad($test->test_program->application->prepared->kod, 3, '0', STR_PAD_LEFT) }}
            </p>
        </div>
        <div style="height: 26px; display: flex; font-weight: 500;font-size: 18px;">
            <p></p>

            <p style="margin-left: 48%;">(ariza beruvchining nomi, kodi)</p>
        </div>

        <div style="display: flex; font-weight: 500;font-size: 20px; height: 35px;">
            <p style="margin-right: 2%">Namuna olish va identifikatsiya dalolatnomasi raqami: </p>
            <p style="margin-right: 2%; height: 26px; 0px; border-bottom: solid 1px black">
                {{ str_pad($test->number, 3, '0', STR_PAD_LEFT) }}</p>
            <p style="margin-right: 2%">sana </p>
            <p style="margin-right: 2%; height: 26px; border-bottom: solid 1px black">{{ $dateDalolatnoma }}</p>
        </div>
        <div style="display: flex; font-weight: 500;font-size: 20px;">
            <p>Namuna tanlab olish &nbsp {{ $test->test_program->application->prepared->name }} paxta tozalash
                korxonasining
                {{ $test->laboratory_final_results->from . ' ' . $test->laboratory_final_results->vakili . ' ' . $test->laboratory_final_results->vakil_name }}
                tomonidan, O'zDSt 614 ning 4 bandi bo'yicha namuna tanlab olish rejasiga muvofiq olingan. <br>
                Sertifikatlashtirish MH: O'z DSt 604 “Paxta tolasi. Texnikaviy shartlar”
            </p>
        </div>
        <div style="display: flex; font-weight: 500;font-size: 20px;">
            <p>
                Sinovlar <span
                    style="height: 26px; 0px; border-bottom: solid 1px black">{{ $test->laboratory_final_results->harorat }}</span>
                °C temperatura, <span
                    style="height: 26px; 0px; border-bottom: solid 1px black">{{ $test->laboratory_final_results->namlik }}</span>
                %
                namlik, <span
                    style="height: 26px; 0px; border-bottom: solid 1px black">{{ $test->laboratory_final_results->yoruglik }}</span>
                lx yoruglik sharoitida o'tkazildi. <br>
                Seleksiya navi <span
                    style="height: 26px; 0px; border-bottom: solid 1px black;">{{ $test->selection->name }}</span>
                Hosil yili <span
                    style="height: 26px; 0px; border-bottom: solid 1px black;">{{ $test->test_program->application->crops->year }}</span>
                y.
            </p>
        </div>
        <div style="display: flex; font-weight: 500;font-size: 20px;">
            <p>Laboratoriyadagi qayd etish raqami <span
                    style="height: 26px; 0px; border-bottom: solid 1px black;">{{ $test->laboratory_final_results->number }}</span>
                <br>
                To'dadagi 100% toylarning sinov natijalarini o'rtacha ma'lumoti:
            </p>

        </div>
        <table class="table table-border " style="border: 1px solid black ;text-align: center;font-size: 18px;">
            <tr>
                {{-- <th style="font-weight: bold; font-size: 20px; width: 40px;">T\r</th> --}}
                <th style="font-weight: bold; font-size: 20px; width: 130px;" rowspan="2"> To'da raqami</th>
                <th style="font-weight: bold; font-size: 20px; width: 110px;"rowspan="2">To'dadagi toylar soni (dona.)
                </th>
                <th style="font-weight: bold; font-size: 20px; width: 80px;"rowspan="2">Netto massai (kg)</th>
                <th style="font-weight: bold; font-size: 20px; width: 90px;"rowspan="2">Seleksiya kodi</th>
                <th style="font-weight: bold; font-size: 20px; width: 90px;" rowspan="1"> Tip</th>
                <th style="font-weight: bold; font-size: 20px; width: 100px;" rowspan="1"> Nav O'zDSt 632:2021 п 8.1
                </th>
                <th style="font-weight: bold; font-size: 20px; width: 100px;" rowspan="1"> Sinf O'zDSt 632:2021 п 8.6
                </th>
                <th style="font-weight: bold; font-size: 20px; width: 120px;" rowspan="1"> Shtapel uzunlik kodda
                    O'zDSt 632:2021 п 8.3</th>
                <th style="font-weight: bold; font-size: 20px; width: 100px;" rowspan="1"> Mic- roneer O'zDSt
                    3295:2018 п 8.3</th>
                <th style="font-weight: bold; font-size: 20px; width: 100px;" rowspan="1"> Solishtirma uzulish kuchi,
                    gf/tex O'zDSt 3295:2018 п 8.5</th>
                <th style="font-weight: bold; font-size: 20px; width: 100px;" rowspan="1"> Uzunlik bo'yicha birhillik
                    indeksi, % O'zDSt 3295:2018 п 8.4</th>
                <th style="font-weight: bold; font-size: 20px; width: 100px;" rowspan="1"> Namlikni massaviy nisbati,
                    % O'zDSt 634:2021 п 7.3</th>
                <th style="font-weight: bold; font-size: 20px; width: 100px;" rowspan="1"> Yoqori o'rtacha uzunlik
                    (dyoym) O'zDSt 3295:2018 п 8.4</th>
            </tr>
            <tr>
                <th style="font-weight: bold; font-size: 20px;" rowspan="1"> 1а,1б,1, 2,3,4, 5,6,7</th>
                <th style="font-weight: bold; font-size: 20px;" rowspan="1"> Nav</th>
                <th style="font-weight: bold; font-size: 20px;" rowspan="1"> sinf</th>
                <th style="font-weight: bold; font-size: 20px;" rowspan="1"> 32-43</th>
                <th style="font-weight: bold; font-size: 20px;" rowspan="1"> 3,5-4,9</th>
                <th style="font-weight: bold; font-size: 20px;" rowspan="1"> 23,0-33,0</th>
                <th style="font-weight: bold; font-size: 20px;" rowspan="1"> 77,0-86,0</th>
                <th style="font-weight: bold; font-size: 20px;" rowspan="1"> 5,0-8,5</th>
                <th style="font-weight: bold; font-size: 20px;" rowspan="1"> </th>
            </tr>
            @if ($final_result)
                @foreach ($final_result as $item)
                    <tr>
                        <td>{{ $item->dalolatnoma->number }}</td>
                        <td>{{ $item->count }}</td>
                        <td>{{ $item->amount }}</td>
                        <td>{{ $item->dalolatnoma->selection_code }}</td>
                        <td></td> {{-- - Tip - --}}
                        <td>{{ $item->sort }}</td>
                        <td>{{ $item->class }}</td>
                        <td>{{ round($item->staple) }}</td>
                        <td>{{ round($item->mic,1) }}</td>
                        <td>{{ round($item->strength,1) }}</td>
                        <td>{{ round($item->uniform,1) }}</td>
                        <td>{{ round($item->humidity/10,1) }}</td>
                        <td>{{ round($item->dalolatnoma->laboratory_result->fiblength/100,2) }}</td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <td colspan="5" style="text-align: start;">O'lchovlarning noaniqligi</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>

                <td>{{ round($measurement_mistake->mic,2) }}</td>
                <td>{{ round($measurement_mistake->strength,1) }}</td>
                <td>{{ round($measurement_mistake->uniform,1) }}</td>
                <td>{{ round($measurement_mistake->humidity,2) }}</td>
                <td>{{ round($measurement_mistake->fiblength,3) }}</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: start;">O'lchov natijalarining MH talablariga muvofiqligi</td>
                <td>
                    @if ($final_result[0]->sort < 6)
                        muvofiq
                    @else
                        nomuvofiq
                    @endif
                </td>
                <td>
                    @if ($final_result[0]->class < 5)
                        muvofiq
                    @else
                        nomuvofiq
                    @endif
                </td>
                <td>
                    @if (20 < $final_result[0]->staple && $final_result[0]->staple < 40)
                        muvofiq
                    @else
                        nomuvofiq
                    @endif
                </td>
                <td>
                    @if (3.5 < $final_result[0]->mic && $final_result[0]->mic < 4.9)
                        muvofiq
                    @else
                        nomuvofiq
                    @endif
                </td>
                <td>
                    @if (23 < $final_result[0]->strength && $final_result[0]->strength < 33)
                        muvofiq
                    @else
                        nomuvofiq
                    @endif
                </td>
                <td>
                    @if (77 < $final_result[0]->uniform && $final_result[0]->uniform < 86)
                        muvofiq
                    @else
                        nomuvofiq
                    @endif
                </td>
                <td>
                    @if (5.0 < ($final_result[0]->humidity/10) && ($final_result[0]->humidity/10) < 8.5)
                        muvofiq
                    @else
                        nomuvofiq
                    @endif
                </td>
                <td>
                    @if (1.08 < ($final_result[0]->dalolatnoma->laboratory_result->fiblength/100) &&
                            ($final_result[0]->dalolatnoma->laboratory_result->fiblength/100) < 1.17)
                        muvofiq
                    @else
                        nomuvofiq
                    @endif
                </td>
            </tr>
        </table>

        <div style="display: flex; font-weight: 500;font-size: 20px;">
            <p>
                <span style="font-weight: 700;">Izox:</span>
                Sinov natijalari tanlab olingan va sinov o'tkazilgan namunalarga taaluqli. Namuna laboratoriya tomonidan
                tanlab olinmagan xollarda, u namuna tanlanshga javobgar emas.
            </p>
        </div>
        <div style="display: flex; font-weight: 500;font-size: 20px;">
            <p>
                Sinov o'tkazilgan sana {{ $formattedDate }} y. <br>
                Qo'shimcha ma'lumotlar: Sinov natijalari bo'yicha qaror qabul qilish uchun asos. Muvofiqlik xulosasi
                <span
                    style="height: 26px; border-bottom: solid 1px black">{{ '№' . optional($test->laboratory_final_results)->number }}</span>
                o'lchovlarning noaniqligi buyurtmachining talabiga binoan ko'rsatiladi.
            </p>
        </div>
        <div style="display: flex; font-weight: 500;font-size: 20px;height: 40px;">
            <p>
                SITX (HVI) texnologik qurilmalar <br>
                o'rnatish katta operatori
            </p>
            <p style="margin-left: 48%; height: 26px; border-bottom: solid 1px black; margin-top: 1%">
                {{ $test->laboratory_final_results->operator->name }}
            </p>
        </div>
        <div style="display: flex; font-weight: 500;font-size: 18px;">
            <p></p>

            <p style="margin-left: 67.3%;">(F.I.SH)</p>
        </div>
        <div style="display: flex; font-weight: 500;font-size: 20px;height: 40px;">
            <p>
                Paxta tolasi Tasniflash bo'yicha mutaxassis <br>
                (klassifikator)
            </p>
            <p style="margin-left: 45%; height: 26px; border-bottom: solid 1px black; margin-top: 1%">
                {{ $test->laboratory_final_results->klassiyor->name }}
            </p>
        </div>
        <div style="display: flex; font-weight: 500;font-size: 18px;">
            <p></p>

            <p style="margin-left: 67.3%;">(F.I.SH)</p>
        </div>


        <h4 style="text-align: center">Sinov bayonnomasi yakuni.</h4>
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
