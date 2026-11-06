
<div id="invoice-cheque" class="py-4 col-12" style="font-family: 'DejaVu Sans', sans-serif;">
    <div>
        <h2 style="text-align: center; margin: 10px 0; font-size: 24px"><b>
                ГОСУДАРСТВЕННОЕ УЧРЕЖДЕНИЕ «ЦЕНТР ОЦЕНКИ КАЧЕСТВА<br> СЕЛЬСКОХОЗЯЙСТВЕННОЙ ПРОДУКЦИИ»
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
            <b>Начальник {{ $test->test_program->application->decision->laboratory->name }}
                <span style="padding: 5px; display: block">
                        {{ $test->laboratory_final_results->director->lastname . '. ' . substr($test->laboratory_final_results->director->name, 0, 1) }}</span>
            </b>
        </div>
    </div>
    <h1 style="padding-top:10px" class="text-center">
        <b>
            <span style="font-size:24px;">  ПРОТОКОЛ ИСПЫТАНИЙ <span style="font-family: 'DejaVu Serif'">№</span>{{ $test->laboratory_final_results->number + $i }}</span>
        </b>
    </h1>

    <div style="width: 100%; display: flex; justify-content: space-between;font-size: 18px;">
        <div style="width: 40%; display: inline-block;">
            <p style=" text-decoration: underline;margin-bottom: 0">{{ $test->test_program->application->decision->laboratory->city->region->name }}.</p>
        </div>

        <div style="width: 50%; display: inline-block; text-align: right">
            <span style="text-decoration: underline">{{ $formattedDate }} г.</span>
        </div>
    </div>

    <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
        <div style="width: 74%; display: inline-block;">
             <b>Наименование заявителя:</b> <span style="font-family: 'DejaVu Serif'">{{ $test->test_program->application->organization->name }}.</span>
        </div>
        <div style="width: 25%; display: inline-block; text-align: right">
            <span><b>Год урожая:</b> {{ $test->test_program->application->crops->year }} г.</span>
        </div>
    </div>
    <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
        <span><b>Наименование и код производителя:</b> {{ $test->test_program->application->prepared->name . ' - ' . str_pad($test->test_program->application->prepared->kod, 3, '0', STR_PAD_LEFT) }}</span>
    </div>
    <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
        <div style="width: 69%; display: inline-block;">
            <span> <b>Номер акта отбора образцов <span style="font-family: 'DejaVu Serif'">№</span> </b>{{ $test->number }}.</span>
        </div>
        <div style="width: 30%; display: inline-block; text-align: left">
            <span> {{ $formattedDate2}} г.</span>
        </div>
    </div>
    @if(getApplicationType() != \App\Models\CropsName::CROP_TYPE_4)
        <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
            <div style="width: 49%; display: inline-block;">
                <span> <b>Название селекции:  </b><span style="font-family: 'DejaVu Serif'">{{ $test->selection->name  }}</span>.</span>
            </div>
            <div style="width: 50%; display: inline-block;">
                <span> <b>Код лаборатории:  </b> {{  str_pad($test->test_program->application->decision->laboratory->kod, 2, '0', STR_PAD_LEFT) }} </span>
            </div>
        </div>
    @endif


        <span style="padding: 10px 0;">Результаты испытаний:</span>
    @if(getApplicationType() != \App\Models\CropsName::CROP_TYPE_4)
        <table class="table table-border " style="border: 1px solid black ;text-align: center;font-size: 18px;">
            <tr>
                <th rowspan="2"> <span style="font-family: 'DejaVu Serif'">№</span></th>
                <th colspan="2"> Предприятие</th>
                <th rowspan="2"> Количество кип в партии (шт.)</th>
                <th rowspan="2"> Масса нетто (кг)</th>
                <th rowspan="2"> Сорт</th>
                <th rowspan="2"> Класс</th>
                <th colspan="2"> Средняя длина верхней половины </th>
                <th rowspan="2"> Микронейр</th>
                <th rowspan="2"> Удельная разрывная нагрузка гс/текс</th>
                <th rowspan="2"> Индекс равномерности по длине %</th>
            </tr>
            <tr>
                <th rowspan="1">код</th>
                <th rowspan="1"> номер партии</th>
                <th rowspan="1"> Дюйм</th>
                <th rowspan="1"> Код</th>
            </tr>
            @if ($group)
            @foreach ($group as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{  str_pad($test->test_program->application->prepared->kod, 3, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $test->test_program->application->crops->party_number}}</td>
                <td>{{ $item->count }}</td>
                <td>{{  $item->amount ? number_format($item->amount - $item->count * $test->tara, 1, ',', ' ') : 0 }}</td>
                <td>{{ $item->sort }}</td>
                <td>{{ optional(\App\Models\CropsGeneration::where('kod','=',$item->class)->first())->name  }}</td>
                <td>{{  number_format($item->dalolatnoma->laboratory_result->fiblength/100, 2, ',', '.') }} </td>
                <td>{{  optional($test->laboratory_result)->tip->staple }}</td>

                <td>{{ number_format($item->mic ,1, ',', '.') }}</td>
                <td>{{ number_format($item->strength, 1, ',', '.') }}</td>
                <td>{{ number_format($item->dalolatnoma->laboratory_result->uniform, 1, ',', '.') }}</td>
            </tr>
            @endforeach
            @endif
        </table>
    @else
        <table class="table table-border " style="border: 1px solid black ;text-align: center;font-size: 18px;">
            <tr>
                <th rowspan="2"> <span style="font-family: 'DejaVu Serif'">№</span></th>
                <th colspan="2"> Предприятие</th>
                <th rowspan="2"> Количество кип в партии (шт.)</th>
                <th rowspan="2"> Масса нетто (кг)</th>
                <th rowspan="2"> Сорт</th>
                <th rowspan="2"> Класс</th>
                <th colspan="2">Тип (мм) </th>
            </tr>
            <tr>
                <th rowspan="1">код</th>
                <th rowspan="1"> номер партии</th>
                <th rowspan="1"> A <br>7-8 и выше</th>
                <th rowspan="1"> B <br>6-7 и ниже</th>
            </tr>
            @if ($group)
                @foreach ($group as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{  str_pad($test->test_program->application->prepared->kod, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $test->test_program->application->crops->party_number}}</td>
                        <td>{{ $item->count }}</td>
                        <td>{{ $item->amount ? number_format($item->amount - $item->count * $test->tara, 1, ',', ' ') : 0 }}</td>
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
                Результаты испытаний относятся только к образцам, прошедшим испытания.
            </p>
        </div>
        <div class="comments-section">
            <div style=" border-bottom: 1px solid black; margin: 25px 0;">Примечание:</div>
            <div style=" border-bottom: 1px solid black; margin: 25px 0;"></div>
            <div style=" border-bottom: 1px solid black; margin: 25px 0;"></div>
        </div>
    <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px;font-size: 18px;">
        <div style="width: 49%; display: inline-block;">
            <span> <b>Специалист по классификации хлопкового волокна и хлопка-сырца  </b></span>
        </div>
        <div style="width: 50%; display: inline-block;text-align: center">
            <span style="font-family: 'DejaVu Serif'">
                {{ optional($test->laboratory_final_results->klassiyor)->name }}
            </span>
        </div>
    </div>
    @if(getApplicationType() != \App\Models\CropsName::CROP_TYPE_4)
        <div style="width: 100%; display: flex; justify-content: space-between; padding-top:10px; font-size: 18px;">
            <div style="width: 49%; display: inline-block;">
                <span> <b>Оператор технологического оборудования (HVI)  </b></span>
            </div>
            <div style="width: 50%; display: inline-block;text-align: center">
                <span style="font-family: 'DejaVu Serif'"> {{ optional($test->laboratory_final_results->operator)->name }}</span>
            </div>
        </div>
    @endif
</div>

