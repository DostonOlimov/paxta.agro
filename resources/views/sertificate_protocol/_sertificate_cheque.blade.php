<div id="invoice-cheque" class="py-4 col-12 invoice-cheque ">
    <img class="background_image" src="{{ public_path('/img/dashboard/dm_logo.jpg') }}" alt="Background Image">

    <div class="content">
        @if(!isset($t))
            <div class="container head_image" >
                <img src="{{ public_path('/img/dashboard/t1.jpg') }}" alt="image" >
            </div>
        @else
            <div class="container head_image" >
                <img src="{{ asset('/img/dashboard/t1.jpg') }}" alt="image" >
            </div>
        @endif

        <h2 class="header__title">O‘ZBEKISTON RESPUBLIKASI VAZIRLAR MAHKAMASI HUZURIDAGI<br>
            AGROSANOAT MAJMUI USTIDAN NAZORAT QILISH INSPEKSIYASI<br>
            QOSHIDAGI “QISHLOQ XO‘JALIGI MAHSULOTLARI SIFATINI<br>
            BAHOLASH MARKAZI” DAVLAT MUASSASASI
        </h2>


        <h1 class="head__title">SIFAT SERTIFIKATI</h1>
        <h2 class="header__intro" style="font-weight: bold;padding-bottom: 30px;">Reestr raqami: @if(isset($sertNumber)) {{ $application->prepared->region->series }}-{{ substr($sertNumber, 2) }} @endif</h2>
        {{--        @else--}}
        {{--            <h1 class="header__intro text-center" style="color:#f3775b; font-size: 24px"><b>Nomuvofiqlik bayonnomasi</b></h1>--}}
        {{--        @endif--}}
        <div style="width: 100%; display: flex; justify-content: space-between;">
            <div style="width: 50%; display: inline-block;">
                <h2 class="main__intro"><b>Mahsulot nomi:&nbsp; </b> {{ $application->crops->name->name}} </h2>
            </div>
            <div style="width: 40%; display: inline-block;">
                <h2 class="main__intro"><b>KOD TN VED:&nbsp; </b> {{ $application->crops->name->kodtnved}}</h2>
            </div>
        </div>
        <div style="width: 100%; display: flex; justify-content: space-between;">
            <div style="width: 50%; display: inline-block;">
                <h2 class="main__intro"><b>Berilgan sana:&nbsp; </b> {{ $formattedDate }} - yil </h2>
            </div>
            <div style="width: 40%; display: inline-block;">
                <h2 class="main__intro"><b>Paxta hosil yili:&nbsp; </b> {{$application->crops->year}}</h2>
            </div>
        </div>
        <div style="width: 100%; display: flex; justify-content: space-between;">
            <div style="width: 50%; display: inline-block;">
                <h2 class="main__intro"><b>Amal qilish muddati:&nbsp;</b> @if(getApplicationType() != \App\Models\CropsName::CROP_TYPE_4) 6 oy @else 1 yil @endif </h2>
            </div>
            <div style="width: 40%; display: inline-block;">
                <h2 class="main__intro"><b>Sinov bayonnomasi raqami:&nbsp; </b> {{ $application->tests->dalolatnoma->laboratory_final_results->number + $index }} </h2>
            </div>
        </div>

            <h2 class="main__intro"><b>Ishlab chiqaruvchi (arizachi) nomi:&nbsp; </b><span style="font-family: 'DejaVu Serif'"> {{ $application->organization->name }} </span></h2>
        <h2 class="main__intro"><b>STIR:&nbsp;  </b> {{$application->organization->inn}}</h2>
        <h2 class="main__intro"><b>Ishlab chiqaruvchi (arizachi) manzili:&nbsp; </b> {{ $application->organization->fulladdress }} </h2>

        <h1 class="header__intro" style="margin-top: 10px;"> IJROCHINING MA’LUMOTLARI</h1>
        @if(getApplicationType() != \App\Models\CropsName::CROP_TYPE_4)
            <table class="table table-border ">
                <tr>
                    {{-- <th">T\r</th> --}}
                    <th rowspan="2" style="font-family: 'DejaVu Serif'"> №</th>
                    <th colspan="2"> Korxona</th>
                    <th rowspan="2"> To‘dadagi toylar soni (dona)</th>
                    <th rowspan="2"> Netto massasi (kg)</th>
                    <th rowspan="2"> Seleksiya nomi</th>
                    <th colspan="2"> Shtapel uzunligi </th>
                    <th rowspan="2"> Nav&nbsp;</th>
                    <th rowspan="2"> Sinf&nbsp;</th>
                    <th rowspan="2">Mikro-neyr</th>
                </tr>
                <tr>
                    <th>kodi</th>
                    <th> to‘da raqami</th>
                    <th> Tipi</th>
                    <th> Kodi</th>
                </tr>
                @if ($group)
                    @foreach ($group as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ str_pad($application->prepared->kod, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $application->crops->party_number}}</td>
                            <td>{{ $item->count }}</td>
                            <td>{{ $item->amount ? number_format($item->amount - $item->count * $application->tests->dalolatnoma->tara , 1, ',',' ') : 0 }}</td>
                            <td>{{ $application->tests->dalolatnoma->selection->name }}</td>
                            <td>{{ optional($application->tests->dalolatnoma->laboratory_result)->tip->name }}</td>
                            <td>{{ optional($application->tests->dalolatnoma->laboratory_result)->tip->staple }}</td>
                            <td>{{ $item->sort }}</td>
                            <td>{{ optional(\App\Models\CropsGeneration::where('kod','=',$item->class)->first())->name  }}</td>
                            <td>{{ number_format($item->mic, 1, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif
            </table>
        @else
            <table class="table table-border ">
                <tr>
                    {{-- <th">T\r</th> --}}
                    <th rowspan="2" style="font-family: 'DejaVu Serif'"> №</th>
                    <th colspan="2"> Korxona</th>
                    <th rowspan="2"> To‘dadagi toylar soni (dona)</th>
                    <th rowspan="2"> Netto massasi (kg)</th>
                    <th rowspan="2"> Nav&nbsp;</th>
                    <th rowspan="2"> Sinf&nbsp;</th>
                    <th rowspan="2">Tipi</th>
                </tr>
                <tr>
                    <th>kodi</th>
                    <th> to‘da raqami</th>
                </tr>
                @if ($group)
                    @foreach ($group as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ str_pad($application->prepared->kod, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $application->crops->party_number}}</td>
                            <td>{{ $item->count }}</td>
                            <td>{{ $item->amount ?  number_format($item->amount - $item->count * $application->tests->dalolatnoma->tara , 1, ',',' ') : 0 }}</td>
                            <td>{{ $item->sort }}</td>
                            <td>{{ optional(\App\Models\CropsGeneration::where('kod','=',$item->class)->where('crop_id','=',4)->first())->name  }}</td>
                            <td>B</td>
                        </tr>
                    @endforeach
                @endif
            </table>
        @endif


        <h5 style="margin-top: 10px; line-height: 1.2">*Izoh: sifat sertifikati paxta mahsulotlari laboratoriyalarining sinov bayonnomasisiz haqiqiy hisoblanmaydi.</h5>
        <div style="width: 100%; display: flex; justify-content: space-between;">
            <div style="width: 45%; display: inline-block; padding-bottom: 30px;">
                <b>Ijrochi :</b>
                {{ $application->decision->laboratory->name }} boshlig‘i:
                {{ optional(optional($application->tests->dalolatnoma->laboratory_final_results)->director)->lastname . ' ' . substr(optional(optional($application->tests->dalolatnoma->laboratory_final_results)->director)->name,0,1). '.' }}
            </div>
            <div style="width: 20%; display: inline-block;"></div>

            <div style="width: 30%;padding-top:60px; text-align: center; display: inline-block;">
                @if(!isset($t))
                    <img src="data:image/png;base64,{{ $qrCode }}" style="height: 100px;" alt="QR Code"><br>
                @endif

                <span style="display: block; margin-top: 5px;margin-left: 120px;"> @if(isset($sertNumber)) {{ substr($sertNumber, 2) }} @endif</span>

            </div>
        </div>
    </div>
</div>
