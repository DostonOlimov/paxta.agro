<div id="invoice-cheque">

    <img class="background_image" src="{{ public_path('/img/dashboard/dm_logo.jpg') }}" alt="Background">

    <!-- Organization Header -->
     <div class="container head_image" >
        <img class="head_image" src="{{ asset('/img/dashboard/t1.jpg') }}" alt="image" >
    </div>
        <h2 class="header__title">O‘ZBEKISTON RESPUBLIKASI VAZIRLAR MAHKAMASI HUZURIDAGI<br>
            AGROSANOAT MAJMUI USTIDAN NAZORAT QILISH INSPEKSIYASI<br>
            QOSHIDAGI “QISHLOQ XO‘JALIGI MAHSULOTLARI SIFATINI<br>
            BAHOLASH MARKAZI” DAVLAT MUASSASASI
        </h2>

    <!-- Document Title -->
    <h2 class="head__title2">
        <span>Paxta mahsuloti va uni qayta ishlashdan olingan (ikkilamchi) mahsulotlarni sinash natijalari bo'yicha</span><br>
        <span class="head__title">XULOSA № {{ $test->laboratory_final_results->number }}</span>
    </h2>

    <!-- Region + Date -->
    <table class="row">
        <tr>
            <td class="left w-65 underline">
                {{ $test->test_program->application->decision->laboratory->city->region->name }}.
            </td>
            <td class="right w-35 underline">
                {{ $formattedDate }} y.
            </td>
        </tr>
    </table>

    <!-- Applicant + Application ID -->
    <table class="row">
        <tr>
            <td class="left">
                <span class="bold">Buyurtma beruvchining nomi:</span>
                <span>{{ $test->test_program->application->organization->name }}</span>
            </td>
        </tr>
    </table>
    <table class="row">
        <tr>
            <td class="left">
                <span class="bold">Buyurtma raqami:</span>
                 {{ $test->final_conclusion_result?->order_number }} {{ date_format(date_create($test->test_program->application->date), 'd.m.Y') }} y.
            </td>
        </tr>
    </table>

    <!-- Invoice + Vehicle -->
    <table class="row">
        <tr>
            <td class="left w-35">
                <span class="bold">Invoys raqami:</span>
                {{ $test->final_conclusion_result?->invoice_number ?? '—' }}
            </td>
            <td class="right w-65">
                <span class="bold">Avtotransport raqami:</span>
                {{ $test->final_conclusion_result->vehicle_number ?? '-' }}
            </td>
        </tr>
    </table>

    <!-- Batch info -->
    <table class="row">
        <tr>
            <td class="left w-50">
                <span class="bold">To'da raqami:</span>
                {{ $test->test_program->application->crops->party_number }}
            </td>
            <td class="right w-50">
                <span class="bold">Toy soni:</span>
                {{ $test->test_program->application->crops->toy_count }} ta
            </td>
        </tr>
    </table>

    <!-- Amount -->
    <table class="row">
        <tr>
             <td class="left">
                <span class="bold">To'da og'irligi(netto):</span>
                {{ number_format($test->test_program->application->crops->amount, 0, '.', ' ') }} kg
            </td>
            @if($test->final_conclusion_result?->cmr_number)
                <td class="right">
                    <span class="bold">CMR №:</span>
                    {{ $test->final_conclusion_result?->cmr_number ?? '—' }}
                </td>
            @endif
        </tr>
    </table>

    <!-- Conclusion Parts -->
    <table class="conclusion-table">
        @if ($test->final_conclusion_result->conclusion_part_1)
            <tr>
                <td> <span> {{ $formattedDate2 }}</span> {{ $test->final_conclusion_result->conclusion_part_1 }}</td>
            </tr>
        @endif

        @if ($test->final_conclusion_result->conclusion_part_2)
            <tr>
                <td>{{ $test->final_conclusion_result->conclusion_part_2 }}</td>
            </tr>
        @endif

        @if ($test->final_conclusion_result->conclusion_part_3)
            <tr>
                <td><b>{{ $test->final_conclusion_result->conclusion_part_3 }}</b></td>
            </tr>
        @endif
    </table>

    <p class="bold" style="margin-top:15px;">
        Sinov natijalari, sinovdan o'tkazilgan namunalarga tegishlidir.
    </p>

    <!-- Signatures -->
    <div class="signature-section">

        <table>
            <tr>
                <td style="width: 40%;" class="bold">
                    {{ $test->test_program->application->decision->laboratory->name }} boshlig'i
                </td>

                <td style="width: 30%;" class="center">
                    @if (!empty($qrCode))
                        <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
                    @endif
                    @if (isset($sert_number))
                        <div>{{ substr($sert_number, 2) }}</div>
                    @endif
                </td>

                <td style="width: 30%;" class="right director-name">
                    {{ $test->laboratory_final_results->director->lastname }}
                    {{ substr($test->laboratory_final_results->director->name, 0, 1) }}.
                </td>
            </tr>
            <tr>
                <td class="bold" style="width: 40%;">Laboratoriya mutaxassisi</td>
                <td ></td>
                <td class="bold" style="width: 30%;">
                    {{ optional($test->laboratory_final_results->operator)->name ?? '_________________' }}
                </td>
            </tr>
        </table>

    </div>

</div>
