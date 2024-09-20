@php
use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp
<style>
    body,
    p,
    span,
    div {
        font-size: 28px !important;
    }
</style>
<div id="invoice-cheque" class="py-4 col-12 {{ $classes ?? '' }}" style=" font-family: Times New Roman;">
    <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-4" style="display: flex; flex-direction: column; justify-content: end;">
            <div class="text-center"> {!! $qrCode !!}</div>
        </div>
        <div class="col-sm-4" style="display:block;">
            <span style="display: block" class="text-center"><b>PSK-2-06-F1 ilovasi</b></span>
            {{-- <span style="display: block" class="text-center"><b>1/1</b></span> --}}
            <h2 class="text-center" style="font-size: 27px">
                <b> «T A S D I Q L A Y M A N»<br>
                    {{ $test->test_program->application->decision->laboratory->name }} boshlig‘i
                    <span style="padding: 5px; display: block">
                        {{ $test->laboratory_final_results->director->lastname . '. ' . substr($test->laboratory_final_results->director->name, 0, 1) }}</span>
                    <span style="padding: 5px;" id="application-date"></span> yil
            </h2>
        </div>
    </div>

    <div>
        <h2 style="text-align: center; margin-top: 10px; font-size: 28px"><b>
                {{-- Oʻzbekiston Respublikasi Qishloq xoʻjaligi vazirligi huzuridagi Аgrosanoat majmui ustidan
            nazorat qilish Inspektsiyasi qoshidagi<br> --}}
                “Qishloq xoʻjaligi mahsulotlari sifatini baholash markazi” davlat muassasasining.<br>
                {{ $test->test_program->application->decision->laboratory->name }} <br>
                <span style="font-size: 23px !important">Davlat reestri raqami {{ $test->test_program->application->decision->laboratory->certificate . ' ' . $test->test_program->application->decision->laboratory->full_address }}</span>
            </b>
        </h2>
        <div style="display: flex; justify-content: end; padding-right: 12%;">
            <p style="font-size: 24px !important;">Laboratoriya kodi <span style="border-bottom: 1px solid black; padding: 0 4px; font-size: 24px !important;">{{ $test->test_program->application->decision->laboratory->kod }}</span></p>
        </div>
    </div>

    <h1 style="padding-top: 30px" class="text-center"><b>PAXTA TOLASINING</b><br></h1>
    <h1 class="text-center"><b><span style="border-bottom: 1px solid black; padding: 0px 18px; ">0{{ optional($test->laboratory_final_results)->number }}</span> - SONLI SINOV BAYONNOMASI </b>
    </h1>
    @php
    use Carbon\Carbon as CarbonAlias;
    CarbonAlias::setLocale('uz');

    // First date
    $formattedDate = CarbonAlias::parse($test->laboratory_final_results->date);
    $day1 = $formattedDate->day;
    $month1 = $formattedDate->format('m');
    $year1 = $formattedDate->year;

    // Second date
    $dateDalolatnoma = CarbonAlias::parse($test->date);
    $day2 = $dateDalolatnoma->day;
    $month2 = $dateDalolatnoma->format('m');
    $year2 = $dateDalolatnoma->year;
    @endphp
    <div>
        <div style="display: flex; justify-content: space-between; font-weight: 500;font-size: 22px; height: fit-content; margin-bottom: 15px">
            <p style="border-bottom: 1px solid black; margin-bottom: 0">{{ $test->test_program->application->decision->laboratory->city->region->name }}.</p>
            <div class="documentDate" style="display: flex; column-gap: 4px;">
                <span>
                    <<<span style="display: inline-block; border-bottom: 1px solid black; padding: 0px 24px; height: fit-content;">{{ $day1 }}
                </span>>></span>
                <span style="border-bottom: 1px solid black; padding: 0px 28px; height: fit-content;">{{ $month1 }}</span>
                <span style="border-bottom: 1px solid black; padding: 0px 8px 0px 12px; height: fit-content;">{{ $year1 }}y</span>
            </div>

        </div>
        <div style="display: flex; font-weight: 500;font-size: 20px; margin-bottom: 0; height: fit-content;">
            <p>Yetkazib beruvchining nomi</p>

            <div style="display: flex; flex-direction:column; row-gap: 12px; align-items: center; margin-left: 15%; ">
                <span style="height: 36px; padding-bottom: 0px; border-bottom: solid 1px black">
                    {{ $test->test_program->application->organization->name . ' ' . str_pad($test->test_program->application->prepared->kod, 3, '0', STR_PAD_LEFT) }}
                </span>
                <span style="font-size: 23px !important; line-height: 0px;">(ariza beruvchining nomi, kodi)</span>
            </div>
        </div>


        <div style="display: flex; font-weight: 500;font-size: 20px; height: 35px; align-items: flex-start;">
            <p style="margin-right: 2%">Namuna olish va identifikatsiya dalolatnomasi raqami: </p>
            <p style="margin-right: 2%; height: 36px; border-bottom: solid 1px black">
                {{ str_pad($test->number, 3, '0', STR_PAD_LEFT) }}
            </p>
            <p style="margin-right: 2%">sana </p>
            <div class="documentDate" style="display: flex; column-gap: 4px;">
                <span>
                    <<<span style="display: inline-block; border-bottom: 1px solid black; padding: 0px 24px; height: 36px;">{{ $day2 }}
                </span>>></span>
                <span style="border-bottom: 1px solid black; padding: 0px 28px; height: 36px;">{{ $month2 }}</span>
                <span style="border-bottom: 1px solid black; padding: 0px 8px 0px 12px; height: 36px;">{{ $year2 }}y</span>
            </div>

        </div>
        <div style="display: flex; font-weight: 500; font-size: 20px;">
            <p style="margin-bottom: 8px;">Namuna tanlab olish <span style="border-bottom: 1px solid black; padding: 0px 4x; height: 26px;">{{ $test->test_program->application->prepared->name }} paxta tozalash
                    korxonasining {{ $test->laboratory_final_results->from . ' ' . $test->laboratory_final_results->vakili . ' ' . $test->laboratory_final_results->vakil_name }} </span>
                tomonidan, OʻzDSt 614 ning 4 bandi boʻyicha namuna tanlab olish rejasiga muvofiq olingan. <br>
                Sertifikatlashtirish MH: Oʻz DSt 604 “Paxta tolasi. Texnikaviy shartlar”
            </p>
        </div>
        <div style="display: flex; font-weight: 500;font-size: 20px;">
            <p style="margin-bottom: 8px;">
                Sinovlar <span style="height: 26px; 0px; border-bottom: solid 1px black">{{ $test->laboratory_final_results->harorat }}</span>
                °C temperatura, <span style="height: 26px; 0px; border-bottom: solid 1px black">{{ $test->laboratory_final_results->namlik }}</span>
                %
                namlik, <span style="height: 26px; 0px; border-bottom: solid 1px black">{{ $test->laboratory_final_results->yoruglik }}</span>
                lx yoruglik sharoitida oʻtkazildi. <br>
                Seleksiya navi <span style="height: 26px; 0px; border-bottom: solid 1px black;">{{ $test->selection->name }}</span>
                Hosil yili <span style="height: 26px; 0px; border-bottom: solid 1px black;">{{ $test->test_program->application->crops->year }}</span>
                y.
            </p>
        </div>
        <div style="display: flex; font-weight: 500;font-size: 20px;">
            <p>Laboratoriyadagi qayd etish raqami <span style="height: 26px; 0px; border-bottom: solid 1px black;">{{ $test->laboratory_final_results->number }}</span>
                <br>
                Toʻdadagi 100% toylarning sinov natijalarini oʻrtacha ma'lumoti:
            </p>

        </div>
        <table class="table table-border " style="border: 1px solid black ;text-align: center;font-size: 18px;">
            <tr>
                {{-- <th style="font-weight: bold; font-size: 20px; width: 40px;">T\r</th> --}}
                <th style="font-weight: bold; font-size: 20px; width: 130px;" rowspan="2"> Toʻda raqami</th>
                <th style="font-weight: bold; font-size: 20px; width: 110px;" rowspan="2">Toʻdadagi toylar soni (dona.)
                </th>
                <th style="font-weight: bold; font-size: 20px; width: 80px;" rowspan="2">Netto massai (kg)</th>
                <th style="font-weight: bold; font-size: 20px; width: 90px;" rowspan="2">Seleksiya kodi</th>
                <th style="font-weight: bold; font-size: 20px; width: 90px;" rowspan="1"> Tip</th>
                <th style="font-weight: bold; font-size: 20px; width: 100px;" rowspan="1"> Nav OʻzDSt 632:2021 п 8.1
                </th>
                <th style="font-weight: bold; font-size: 20px; width: 100px;" rowspan="1"> Sinf OʻzDSt 632:2021 п 8.6
                </th>
                <th style="font-weight: bold; font-size: 20px; width: 120px;" rowspan="1"> Shtapel uzunlik kodda
                    OʻzDSt 632:2021 п 8.3</th>
                <th style="font-weight: bold; font-size: 20px; width: 100px;" rowspan="1"> Mic- roneer OʻzDSt
                    3295:2018 п 8.3</th>
                <th style="font-weight: bold; font-size: 20px; width: 100px;" rowspan="1"> Solishtirma uzulish kuchi,
                    gf/tex OʻzDSt 3295:2018 п 8.5</th>
                <th style="font-weight: bold; font-size: 20px; width: 100px;" rowspan="1"> Uzunlik boʻyicha birhillik
                    indeksi, % OʻzDSt 3295:2018 п 8.4</th>
                <th style="font-weight: bold; font-size: 20px; width: 100px;" rowspan="1"> Namlikni massaviy nisbati,
                    % OʻzDSt 634:2021 п 7.3</th>
                <th style="font-weight: bold; font-size: 20px; width: 100px;" rowspan="1"> Yoqori oʻrtacha uzunlik
                    (dyoym) OʻzDSt 3295:2018 п 8.4</th>
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
                <td>{{ round($item->dalolatnoma->laboratory_result->humidity/10,1) }}</td>
                <td>{{ round($item->dalolatnoma->laboratory_result->fiblength/100,2) }}</td>
            </tr>
            @endforeach
            @endif
            <tr>
                <td colspan="5" style="text-align: start;">Oʻlchovlarning noaniqligi</td>
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
                <td colspan="5" style="text-align: start;">Oʻlchov natijalarining MH talablariga muvofiqligi</td>
                <td>
                    @if ($final_result[0]->sort < 6) muvofiq @else nomuvofiq @endif </td>
                <td>
                    @if ($final_result[0]->class < 5) muvofiq @else nomuvofiq @endif </td>
                <td>
                    @if (20 < $final_result[0]->staple && $final_result[0]->staple < 40) muvofiq @else nomuvofiq @endif </td>
                <td>
                    @if (3.5 < $final_result[0]->mic && $final_result[0]->mic < 4.9) muvofiq @else nomuvofiq @endif </td>
                <td>
                    @if (23 < $final_result[0]->strength && $final_result[0]->strength < 33) muvofiq @else nomuvofiq @endif </td>
                <td>
                    @if (77 < $final_result[0]->uniform && $final_result[0]->uniform < 86) muvofiq @else nomuvofiq @endif </td>
                <td>
                    @if (5.0 < ($final_result[0]->dalolatnoma->laboratory_result->humidity/10) && ($final_result[0]->dalolatnoma->laboratory_result->humidity/10) < 8.5) muvofiq @else nomuvofiq @endif </td>
                <td>
                    @if (1.08 < ($final_result[0]->dalolatnoma->laboratory_result->fiblength/100) &&
                        ($final_result[0]->dalolatnoma->laboratory_result->fiblength/100) < 1.17) muvofiq @else nomuvofiq @endif </td>
            </tr>
        </table>

        <div style="display: flex; font-weight: 500;font-size: 20px;">
            <p>
                <span style="font-weight: 700; line-height: 0 !important;">Izox:</span>
                Sinov natijalari tanlab olingan va sinov oʻtkazilgan namunalarga taaluqli. Namuna laboratoriya tomonidan
                tanlab olinmagan xollarda, u namuna tanlanshga javobgar emas.
            </p>
        </div>
        <div style="display: flex; flex-direction:column; font-weight: 500;font-size: 20px; margin-top: -18px">
            <div style="display: flex;"><span>Sinov oʻtkazilgan sana</span>
                <div class="documentDate" style="display: flex; column-gap: 8px; margin-left: 14px;">
                    <span>
                        <<<span style="display: inline-block; border-bottom: 1px solid black; padding: 0px 24px; height: 36px;">{{ $day1 }}
                    </span>>></span>
                    <span style="border-bottom: 1px solid black; padding: 0px 28px; height: 36px;">{{ $month1 }}</span>
                    <span style="border-bottom: 1px solid black; padding: 0px 8px 0px 12px; height: 36px;">{{ $year1 }}y.</span>
                </div>
            </div>
            <p>
                Qoʻshimcha ma'lumotlar: Sinov natijalari boʻyicha qaror qabul qilish uchun asos. Muvofiqlik xulosasi <span style="width:fit-content; height: 36px; border-bottom: solid 1px black">{{ '№' . optional($test->laboratory_final_results)->number }}</span> oʻlchovlarning noaniqligi buyurtmachining talabiga binoan koʻrsatiladi. </p>
        </div>
        <div style="display: flex; justify-content: space-between; font-weight: 500;font-size: 20px;height: 40px; margin-bottom: 40px">
            <p style="max-width: 405px; line-height: normal; margin-top: -10px">SITX (HVI) texnologik qurilmalar oʻrnatish katta operatori</p>
            <div style="display: flex; flex-direction:column; row-gap: 0px; align-items: center">
                <span style="border-bottom: 1px solid black; width: 150px; min-height: 36px; margin-bom: 0;"></span>
                <span style="font-size: 22px !important">(imzo)</span>
            </div>
            <div style="display: flex; flex-direction:column; row-gap: 0px; align-items: center">
                <span style="height: 36px; border-bottom: 2px solid black; padding: 0 6px;">{{ $test->laboratory_final_results->operator->name }}</span>
                <span style="font-size: 22px !important">(F.I.SH)</span>
            </div>
        </div>
        <div style="display: flex; justify-content: space-between; font-weight: 500;font-size: 20px;height: 40px; margin-bottom: 80px"">
            <p style=" max-width: 405px; line-height: normal; margin-top: -10px">Paxta tolasi Tasniflash boʻyicha mutaxassis (klassifikator)</p>
            <div style="display: flex; flex-direction:column; row-gap: 0px; align-items: center">
                <span style="border-bottom: 1px solid black; width: 150px; min-height: 36px; margin-bom: 0;"></span>
                <span style="font-size: 22px !important">(imzo)</span>
            </div>
            <div style="display: flex; flex-direction:column; row-gap: 0px; align-items: center">
                <span style="height: 36px; border-bottom: 2px solid black; padding: 0 6px;">{{ $test->laboratory_final_results->klassiyor->name }}</span>
                <span style="font-size: 22px !important">(F.I.SH)</span>
            </div>
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
