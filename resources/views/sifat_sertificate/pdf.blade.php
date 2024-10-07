@extends('layouts.pdf')
@section('styles')
<style>
    .invoice-cheque {
        width: 100% !important;
        margin: 0 auto;
        font-size: 14px;
    }

    .small_notes{
        font-size: 10px;
        display: block;
        padding: 0 !important;
        margin: 0 !important;
    }

    .header__tasdiqlayman {
        position: absolute;
        top: 0;
        right: 0;
        text-align: center;
        width: 40% !important;
    }

    .header__tasdiqlayman h2 {
        font-size: 14px;
        font-weight: bold;
    }

    .header__title {
        text-align: center;
        margin-top: 100px;
        text-transform: uppercase;
    }

    .header__intro {
        display: flex;
        justify-content: center;
        margin: 0 auto;
        text-align: center;
        font-size: 13px;
        max-width: 90%;
        line-height: 1.2;
    }
    .main__intro {
        display: flex;
        justify-content: center;
        margin: 0 auto;
        text-align: left;
        font-size: 14px;
        max-width: 100%;
        line-height: 1.2;
    }

    h1 {
        line-height: 1;
        text-align: center;
        font-size: 14px;
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
        font-size: 10px;
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
<body>

    <div class="invoice-cheque">
        <div class="header_right">
            <div class="col-sm-4"></div>
            <div class="col-sm-4" style="display: flex; flex-direction: column; justify-content: end;">
                <div> {!! $qrCode !!}</div>
            </div>

            <div class="header__tasdiqlayman">
                <span class="small_notes">ПСК 04:2024 1/1</span>
                @php $director = $test->laboratory_results->result_users->where('status', 1)->first(); @endphp
                <h2>
                     «T A S D I Q L A Y M A N»<br> “Qishloq xo‘jaligi ekinlari urug‘lari va ko‘chatlarining ekinbopligini, GMO va
                        havfsizligini aniqlash markaziy
                        laboratoriyasi” boshlig‘i
                        <span style="padding: 3px; display: block">
                            @if ($director)
                                {{ substr($director->users->name, 0, 1) }}. {{ $director->users->lastname }}.
                            @else
                                U.Xasanov.
                            @endif
                             {{ $formattedDate }} yil
                        </span>
                </h2>
            </div>
        </div><br>

        <h1 style="padding-top: 30px" class="header__title">EKILADIGAN URUG‘LARNI SIFAT KO‘RSATKICHLARI TO‘G‘RISIDA</h1>
        <h1 style="line-height: 0.8; margin-top: -2px; margin-bottom: 3px;">{{ optional($test->laboratory_results)->number }} - sonli SINOV BAYONNOMA</h1>
        <div>
            <h2 class="header__intro" style="font-weight: bold;">
              “Qishloq xo'jaligi mahsulotlari sifatini baholash markazi” davlat muassasasi.<br>
                "Qishloq xo‘jaligi ekinlari urug‘lari va
                ko‘chatlarining ekinbopligini, GMO va
                xavfsizligini aniqlash markaziy
                laboratoriyasi".<br>
                Toshkent viloyati, Qibray tumani,Bobur MFY, Bobur ko‘chasi 1-uy. tel: (91) 111-49-93.
                urugsert@agroxizmat.uz Akkreditatsiya guvohnomasi O'ZAK.SL.0271.
            </h2>
            <h2 class="main__intro">Namunalar Markaziy laboratoriyaga Sertifikatlashtirish idorasi tomonidan {{ $start_date }} yilda <br>
                <span style="text-decoration: underline;"> № {{ $test->number }}/1 @if ($test->count > 1) - {{ $test->number }}/{{ $test->count }}
                @endif </span> -sonli raqamlar bilan kodlangan holda urug'lik
                {{ $test->application->crops->name->name }} namunalari taqdim etilgan.
            </h2>
            <h2 class="main__intro">Urug'lik me'yoriy hujjati: <span
                    style="text-decoration: underline; ">{{ $nds_type }}
                    {{ $test->application->crops->name->nds->number }}&nbsp;{{ $test->application->crops->name->nds->name }}.</span>
            </h2>
            <h2 class="main__intro"> Namuna ro'yxatga oligan raqam(lar):
                <span  style="text-decoration: underline; ">
                {{ $test->laboratory_numbers->first()->number }} @if ($test->count > 1)
                    - {{ $test->laboratory_numbers->last()->number }}
                @endif. Namuna og'irligi :
                {{ number_format($test->count * $test->weight, 1, '.', '.') }}
                {{ \App\Models\CropData::getMeasureType($test->measure_type) }}.
                </span>
            </h2>
            <h2 class="main__intro">Mahsulot to'g'risida ma'lumot :
                <span style="text-decoration: underline;">
                     @if ($test->application->crops->pre_name == 'tuksiz')
                        tuksizlantirilgan
                    @else
                        {{ $test->application->crops->pre_name }}
                    @endif urug'lik {{ $test->application->crops->name->name }}
                    {{ $test->application->crops->year }} - hosilidan tayyorlangan.
                </span>
            </h2>
            <h2 class="main__intro">Sinov o'tkazish maqsadi: sertifikatlash. Subpodryad
                bo'yicha
                o'tkazilgan sinovlar :   <span style="text-decoration: underline;"> yo'q.</span></h2>
            <h2 class="main__intro">Sinov o'tkazish sharoiti :  harorati -   <span style="text-decoration: underline;">
                    {{ $test->laboratory_results->harorat }} °C </span> va nisbiy namligi -   <span style="text-decoration: underline;">
                    {{ $test->laboratory_results->namlik }} %.</span>
            </h2>
            <h1>{{ $nds_type }} {{ $test->application->crops->name->nds->number }} bo'yicha SINOV NATIJALARI:</h1>
            @php $t = 1; @endphp
            <table class="align-middle " style="border: 1px solid black; text-align: center;">
                <tr style=" height: 40px;">
                    <th style="font-weight: bold; font-size: 10px; width: 15px;">T\r</th>
                    <th style="font-weight: bold; font-size: 10px;">Ekish sifat ko'rsatkichlari</th>
                    <th style="font-weight: bold; font-size: 10px; width: 90px;">Sinov usullarining me'yoriy hujjatlari</th>
                    <th style="font-weight: bold; font-size: 10px; width: 120px;">MH bo'yicha me'yorlar</th>
                    <th style="font-weight: bold; font-size: 10px;">Sinov natijasi /U</th>
                    <th style="font-weight: bold; font-size: 10px;">Ko'rsatkichlar muvofiqligi</th>
                </tr>
                @foreach ($indicators as $k => $indicator)
                    <tr>
                        <td style="font-weight: bold">
                            @if (!$indicator->indicator->parent_id)
                                {{ $t }}
                            @endif
                        </td>
                        <td style="text-align: left;font-weight: bold;">
                            {{ $indicator->indicator->name }}
                        </td>
                        <td>{!! nl2br($indicator->indicator->nd_name) !!}</td>
                        <td>
                            @if ($indicator->indicator->nd_name)
                                @php
                                    if (
                                    $indicator->indicator->id == 75 and
                                    $test->application->crops->pre_name == 'tuksiz'
                                    ) {
                                    $default = 0.3;
                                    } else {
                                    $addition_value = $indicator->indicator
                                    ->default_value()
                                    ->where('generation_id', $test->application->crops->generation_id)
                                    ->first();
                                    if ($addition_value) {
                                    $default = $addition_value->value;
                                    } else {
                                    $default = $indicator->indicator->default_value;
                                    }
                                    }
                                @endphp
                                @if ($default != 0)
                                    @if ($indicator->indicator->measure_type == 1)
                                        kamida,
                                    @elseif ($indicator->indicator->measure_type == 2)
                                        ko'pi bilan,
                                    @elseif ($indicator->indicator->measure_type == 3)
                                    @endif
                                @endif

                                @if (!is_string($default))
                                    {{ number_format((float) $default, $indicator->indicator->round_type, ',', '.') }}
                                @else
                                    {{ $default }}
                                @endif
                            @endif
                        </td>
                        <td>

                            @if ($indicator->indicator->nd_name)
                                @if ($indicator->result != 0)
                                    {{ number_format($indicator->result, $indicator->indicator->round_type, ',', '.') }}
                                @else
                                    {{ 'uchramadi' }}
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($indicator->indicator->nd_name)
                                {{ $indicator->type == 1 ? 'Muvofiq' : 'Nomuvofiq' }}
                            @endif
                        </td>
                    </tr>
                    @if (!$indicator->indicator->parent_id)
                        @php $t=$t+1; @endphp
                    @endif
                @endforeach
            </table>
            <h2 class="header__intro" style="padding-top: 1px; font-size: 11px;"><span style="font-weight: 700">Sinov o'tkazilgan muddat :</span>
                {{ $start_date }} dan {{ $end_date }} gacha
            </h2>
            <h2 class="header__intro" style="text-align: start !important; font-size: 11px; margin-top: 6px;"><span style="font-weight: 700">Qo'shimcha ma'lumotlar: </span>Mazkur
                urug'lik {{ $test->application->crops->name->name }} partiyasining avlodi sinov dasturiga
                muvofiq {{ $test->application->crops->generation->name }} reproduksiyaga mansub.<br>

            </h2>
            <h2 class="header__intro" style="margin-top: 6px;">Ushbu urug'lik {{ $test->application->crops->name->name }}
                {{ $test->application->crops->pre_name }} @if ($test->application->crops->pre_name)
                    holda
                @endif
                @foreach ($production_type as $type)
                    @if ($type->type_id == 8)
                        {{ optional($type->type)->name }}.
                    @endif
                @endforeach
                @if ($amount_nds)
                    1000 dona vazni o'rtacha ({{ $amount_nds->nd_name }}) <span
                        style="font-weight: 700">{{ $test->indicators->first()->result }} gr</span>. ni tashkil
                    etadi.
            </h2>
            @endif
            <h2 class="header__intro" style="margin-bottom: 6px;">Sinov natijalari bo'yicha qaror qabul qilish uchun asos Muvofiqlik xulosasi
                №{{ optional($test->laboratory_results)->number }}, o'lchovlarning noaniqligi (U)
                buyurtmachining talabiga asosan ko'rsatiladi.</h2>
            <h2 class="header__intro" style="font-weight: 700; font-size: 11px;">Xulosa : <span style="text-decoration: underline;">{{ $test->laboratory_results->data }}</span></h2>
            <h4 class="header__intro" style="font-weight: normal;">Natijalar sinovdan o'tkazilgan na'munalarga tegishli.</h4>
            <div style="display: flex !important; margin: 55px 0; justify-content: space-between !important;">
                @php $muhandis = $test->laboratory_results->result_users->where('status', 2)->first(); @endphp
                @if ($muhandis)
                    <h2 class="header__intro" style="font-weight: bold; font-size: 12px !important; display: inline;">Sinov muxandisi: {{ substr($muhandis->users->name, 0, 1) }}.
                        {{ $muhandis->users->lastname }} {!! QrCode::size(50)->generate(route('show.user', $muhandis->users->id)) !!}
                    </h2>
                @else
                    <h2 class="header__intro" style="font-weight: bold; font-size: 12px !important; display: inline;"> Sinov muxandisi: U.Quziyev{!! QrCode::size(50)->generate(route('show.user', 55)) !!}</h2>
                @endif
                <h2 class="header__intro" style="font-weight: bold; font-size: 12px !important; display: inline; float: right;">Bosh mutaxassis
                    {{ substr($test->laboratory_results->users->name, 1, 1) == 'h' ? substr(optional($test->laboratory_results->users)->name, 0, 2) : substr(optional($test->laboratory_results->users)->name, 0, 1) }}.
                    {{ optional($test->laboratory_results->users)->lastname }} {!! QrCode::size(50)->generate(route('show.user', $test->laboratory_results->users->id)) !!}
                </h2>
            </div>
            <h2 class="header__intro">Sinov mutaxassislar: </h2>
            <div style="display: flex;">
                @foreach ($test->laboratory_results->result_users->where('status', 3) as $key => $result_user)
                    <h2 style="margin-right: 2%; display:inline-block">{{ ++$key }}.
                            {{ substr($result_user->users->name, 1, 1) == 'h' ? substr($result_user->users->name, 0, 2) : substr($result_user->users->name, 0, 1) }}.
                            {{ optional($result_user->users)->lastname }} {!! QrCode::size(50)->generate(route('show.user', $result_user->users->id)) !!}</h2>
                @endforeach
            </div>
            <h4 style="text-align: center; margin-top: 10px;">Sinov bayonnomasi yakuni.</h4>
        </div>
    </div>
</body>
@endsection
