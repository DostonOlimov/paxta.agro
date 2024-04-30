<div id="invoice-cheque" class="py-4 col-12 {{ $classes ?? '' }}" style=" font-family: Times New Roman;">
    <div class="row">
        <div class="col-sm-4"></div>
        <div class="col-sm-4" style="display: flex; flex-direction: column; justify-content: end;">
            <div class="text-center"> {!! $qrCode !!}</div>
        </div>
        <div class="col-sm-4">
            <span style="display: block" class="text-center"><b>ПСК 04:2024</b></span>
            <span style="display: block" class="text-center"><b>1/1</b></span>
            <h2 class="text-center">
                <b> «T A S D I Q L A Y M A N»<br>
                    “Qishloq xo‘jaligi ekinlari urug‘lari va
                    ko‘chatlarining ekinbopligini, GMO va
                    havfsizligini aniqlash markaziy
                    laboratoriyasi” boshlig‘i
                    <span style="padding: 5px;display: block"> U.Xasanov</span>
                    <span style="padding: 5px;" id="application-date"></span> yil
            </h2>
        </div>
    </div>


    <h1 style="padding-top: 30px" class="text-center"><b>EKILADIGAN URUG‘LARNI SIFAT KO‘RSATKICHLARI TO‘G‘RISIDA</b></h1>
    <h1 class="text-center"><b>{{ optional($test->laboratory_results)->number }} - sonli TAHLIL NATIJASI.</b></h1>
    <div>
        <h2 style="font-weight: 700;"> Urug'lik: <b> <span style=" text-decoration: underline;">Buyurtmachi nomi va urug'lik to'g'risidagi ma'lumot kodlashtirilgan.</span></b> </h2>
        <h2> № <span style=" text-decoration: underline;font-weight: bold;">XXXXX</span> urug'lik partiyali,
            <span style=" text-decoration: underline;font-weight: bold;">XXXXX</span> tonnali,
            {{ $test->application->crops->year }} yil hosilidan, {{$test->application->crops->pre_name}}  @if($test->application->crops->pre_name) xolda @endif
            @foreach($production_type as $type)
                @if($type->type_id == 8)
                {{ optional($type->type)->name  }}.
                @endif
            @endforeach
        </h2>
        <h2> <span style="font-weight: 700;">Bu urug'lik:</span>  <span style=" text-decoration: underline;font-weight: bold;">XXXXX</span> tumanida etishtirilgan, №
            <span style=" text-decoration: underline; font-weight: bold;">XXXXX</span> partiyali urug'lik tozalashdan olingan. </h2>
        <h2><span style="font-weight: 700;">Namuna tanlab olingan muddat: </span> Namunalar, Markaziy laboratoriyaga Sertifikatlashtirish idorasi tomonidan
            {{ $start_date }} - yilda № {{ $test->id }}/1 @if ($test->count > 1)
                -{{ $test->id }}/{{ $test->count }}
            @endif -sonli raqamlar bilan kodlangan holda urug'lik chigit namunalari taqdim etilgan.
            Namuna ro'yxatga olingan raqam: {{ $test->laboratory_numbers->first()->number }}.</h2>

        <h1 style="text-align: center"> @if($test->application->crops->name->id == 21) G'o'zaning seleksiyon @else {{ $test->application->crops->name->id }} @endif navi:
            <span style=" text-decoration: underline;font-weight: bold;">XXXXX</span>  , avlodi: {{ $test->application->crops->generation->name }}, nav
            tozaligi: <span style=" text-decoration: underline;font-weight: bold;">XXXXX</span>  %</h1>
        {{-- <h2 style="text-align: center">{{ $nds_type }} {{$test->application->crops->name->nds->number}} bo'yicha TAHLIL NATIJASI:</h2> --}}
        @php $t = 1; @endphp
        <table class=" align-middle" style="border: 1px solid black ;text-align: center;font-size: 18px; width: 100%">
            <tr style=" height: 40px;">
                <th style="font-weight: bold; font-size: 20px;">T\r</th>
                <th style="font-weight: bold; font-size: 20px;"> Ekish sifat ko'rsatkichlari</th>
                <th style="font-weight: bold; font-size: 20px;">MH bo'yicha me'yorlar</th>
                <th style="font-weight: bold; font-size: 20px;">Sinov natijasi /U</th>
                <th style="font-weight: bold; font-size: 20px;">Ko'rsatkichlar muvofiqligi</th>

            </tr>
            @foreach($indicators as $k => $indicator)
                <tr>
                    <td >@if(!$indicator->indicator->parent_id) {{$t}} @endif</td>
                    <td style="text-align: left;font-weight: bold;padding-left: 10px;">{{$indicator->indicator->name}} @if ($indicator->indicator->measure_type==1), kamida, % @elseif ($indicator->indicator->measure_type==2), ko'pi bilan, % @elseif ($indicator->indicator->measure_type==4) , % @endif</td>
                    <td>
                        @if($indicator->indicator->nd_name)
                            {{$indicator->indicator->default_value}}
                        @endif
                    </td>
                    <td>
                            @if($indicator->indicator->nd_name)
                                @if($indicator->result != 0)
                                    {{ number_format( $indicator->result,  $indicator->indicator->round_type, '.', ' ') }}
                                @else
                                    @if($indicator->indicator->measure_type==1 || $indicator->indicator->measure_type==2)
                                        {{'aniqlanmadi'}}
                                    @else
                                        {{'uchramadi'}}
                                     @endif
                                @endif
                            @endif

                    </td>
                    <td>
                        @if($indicator->indicator->nd_name)
                            {{($indicator->type == 1 && $indicator->result==0) ? 'Muvofiq' : 'Nomuvofiq'}}
                        @endif
                    </td>
                </tr>
                @if(!$indicator->indicator->parent_id) @php $t=$t+1; @endphp @endif
            @endforeach
        </table>
        {{-- start --}}
        <h2 style="padding-left: 50px;padding-top:20px;">Ushbu partiya bo'yicha 1
            ({{ $test->count * $test->weight }}-{{ \App\Models\CropData::getMeasureType($test->measure_type) }})
            o'rtacha namuna sinovdan o'tkazildi</h2>
        <h2 style="padding-left: 50px;"> Urug'chilik laboratoriyasining xulosasi: <span style="text-decoration: underline;"> <b>{{ $test->laboratory_results->data }}</b></span></h2>
        {{-- end --}}
        <h2 style="padding-left: 50px;">Ushbu shakl bo'yicha berildi    <span style="text-decoration: underline;"> <b>  Sertifikatlashtirish idorasi ga.</b></span></h2>
        {{--  --}}
        <h4 style="padding-left: 50px;">Natijalar sinovdan o'tkazilgan na'munalarga tegishli.</h4>

        <div style="display: flex; ">
            <h2><b> Sinov muxandisi:  U.Quziyev</b> {!! QrCode::size(50)->generate(route('show.user', 55)) !!}</h2> {{-- {{ substr(optional($test->laboratory_results->users)->name, 0, 1) }}.  {{ optional($test->laboratory_results->users)->lastname }}--}}
            <h2 style="margin-left: 2%"><b>Bosh mutaxassis</b>  {{(substr($test->laboratory_results->users->name, 1, 1)=='h')? substr(optional($test->laboratory_results->users)->name, 0, 2) : substr(optional($test->laboratory_results->users)->name, 0, 1)}}. {{optional($test->laboratory_results->users)->lastname}}</b> {!! QrCode::size(50)->generate(route('show.user', $test->laboratory_results->users->id)) !!}</h2>
        </div>
        <h2 style="font-weight: 700;">Sinov mutaxassislar: </h2>
        <div style="display: flex;">
            @foreach($test->laboratory_results->result_users as $key => $result_user)

                <h2 style="margin-right: 2%"><b>{{ ++$key }}. {{ (substr($result_user->users->name, 1, 1)=='h')? substr($result_user->users->name, 0, 2):substr($result_user->users->name, 0, 1) }}. {{ optional($result_user->users)->lastname }}</b> {!! QrCode::size(50)->generate(route('show.user', $result_user->users->id)) !!}</h2>
            @endforeach
        </div>

        <h4 style="text-align: center; padding-left: 50px">Sinov tahlil natijasi yakuni.</h4>
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
