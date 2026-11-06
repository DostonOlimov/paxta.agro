<div id="invoice-cheque" class="py-4 col-12 invoice-cheque " style="font-family: 'DejaVu Sans', sans-serif;">
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

        <h2 class="header__title">ГОСУДАРСТВЕННОЕ УЧРЕЖДЕНИЕ «ЦЕНТР ОЦЕНКИ КАЧЕСТВА<br>
            СЕЛЬСКОХОЗЯЙСТВЕННОЙ ПРОДУКЦИИ» ПРИ ИНСПЕКЦИИ ПО<br>
            КОНТРОЛЮ ЗА АГРОПРОМЫШЛЕННЫМ КОМПЛЕКСОМ ПРИ<br>
            КАБИНЕТЕ МИНИСТРОВ РЕСПУБЛИКИ УЗБЕКИСТАН
        </h2>


        <h1 class="head__title">СЕРТИФИКАТ КАЧЕСТВА</h1>
        <h2 class="header__intro" style="font-weight: bold;padding-bottom: 30px;">Реестровый номер: @if(isset($sertNumber)) {{ $application->prepared->region->series }}-{{ substr($sertNumber, 2) }} @endif</h2>
        {{--        @else--}}
        {{--            <h1 class="header__intro text-center" style="color:#f3775b; font-size: 24px"><b>Nomuvofiqlik bayonnomasi</b></h1>--}}
        {{--        @endif--}}
        <div style="width: 100%; display: flex; justify-content: space-between;">
            <div style="width: 50%; display: inline-block;">
                <h2 class="main__intro"><b>Наименование продукции:&nbsp; </b> {{ $application->crops->name->name}} </h2>
            </div>
            <div style="width: 40%; display: inline-block;">
                <h2 class="main__intro"><b>KOD TN VED:&nbsp; </b> {{ $application->crops->name->kodtnved}}</h2>
            </div>
        </div>
        <div style="width: 100%; display: flex; justify-content: space-between;">
            <div style="width: 50%; display: inline-block;">
                <h2 class="main__intro"><b>Дата выдачи:&nbsp; </b> {{ $formattedDate }} г. </h2>
            </div>
            <div style="width: 40%; display: inline-block;">
                <h2 class="main__intro"><b>Год урожая хлопка:&nbsp; </b> {{$application->crops->year}}</h2>
            </div>
        </div>
        <div style="width: 100%; display: flex; justify-content: space-between;">
            <div style="width: 50%; display: inline-block;">
                <h2 class="main__intro"><b>Срок действия:&nbsp;</b> @if(getApplicationType() != \App\Models\CropsName::CROP_TYPE_4) 6 месяцев @else 1 год @endif </h2>
            </div>
            <div style="width: 40%; display: inline-block;">
                <h2 class="main__intro"><b>Номер протокола испытаний:&nbsp; </b> {{ $application->tests->dalolatnoma->laboratory_final_results->number + $index }} </h2>
            </div>
        </div>

            <h2 class="main__intro"><b>Наименование производителя (заявителя):&nbsp; </b><span style="font-family: 'DejaVu Serif'"> {{ $application->organization->name }} </span></h2>
        <h2 class="main__intro"><b>ИНН:&nbsp;  </b> {{$application->organization->inn}}</h2>
        <h2 class="main__intro"><b>Адрес производителя (заявителя):&nbsp; </b> {{ $application->organization->fulladdress }} </h2>

        <h1 class="header__intro" style="margin-top: 10px;"> ДАННЫЕ ИСПОЛНИТЕЛЯ</h1>
        @if(getApplicationType() != \App\Models\CropsName::CROP_TYPE_4)
            <table class="table table-border ">
                <tr>
                    {{-- <th">T\r</th> --}}
                    <th rowspan="2" style="font-family: 'DejaVu Serif'"> №</th>
                    <th colspan="2"> Предприятие</th>
                    <th rowspan="2"> Количество кип в партии (шт.)</th>
                    <th rowspan="2"> Масса нетто (кг)</th>
                    <th rowspan="2"> Название селекции</th>
                    <th colspan="2"> Длина штапеля </th>
                    <th rowspan="2"> Сорт&nbsp;</th>
                    <th rowspan="2"> Класс&nbsp;</th>
                    <th rowspan="2">Микронейр</th>
                </tr>
                <tr>
                    <th>код</th>
                    <th> номер партии</th>
                    <th> Тип</th>
                    <th> Код</th>
                </tr>
                @if ($group)
                    @foreach ($group as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ str_pad($application->prepared->kod, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $application->crops->party_number}}</td>
                            <td>{{ $item->count }}</td>
                            <td>{{ $item->amount ? number_format($item->amount - $item->count * $application->tests->dalolatnoma->tara , 1, ',',' ') : 0 }}</td>
                            <td><span style="font-family: 'DejaVu Serif'">{{ $application->tests->dalolatnoma->selection->name }}</span></td>
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
                    <th colspan="2"> Предприятие</th>
                    <th rowspan="2"> Количество кип в партии (шт.)</th>
                    <th rowspan="2"> Масса нетто (кг)</th>
                    <th rowspan="2"> Сорт&nbsp;</th>
                    <th rowspan="2"> Класс&nbsp;</th>
                    <th rowspan="2">Тип</th>
                </tr>
                <tr>
                    <th>код</th>
                    <th> номер партии</th>
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


        <h5 style="margin-top: 10px; line-height: 1.2">*Примечание: сертификат качества недействителен без протокола испытаний лаборатории хлопковой продукции.</h5>
        <div style="width: 100%; display: flex; justify-content: space-between;">
            <div style="width: 45%; display: inline-block; padding-bottom: 30px;">
                <b>Исполнитель:</b>
                Начальник {{ $application->decision->laboratory->name }}:
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
