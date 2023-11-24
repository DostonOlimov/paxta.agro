<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: "DejaVu Sans", "Helvetica", serif;
            height: auto;
            font-size: 10pt!important;
        }

        .text-center {
            text-align: center;
        }

        table {
            border: 1px solid #e3e3f7;
            width: 100%;
            border-collapse: collapse;
            word-wrap: break-word;
            display: table;
        }

        table th, table td {
            border: 1px solid #e3e3f7;
        }

        table tr td {
            padding: 5px;
        }

        .w-1-4 {
            width: 200px !important;
            display: inline-block;
            box-sizing: border-box;
        }

        .w-3-4 {
            display: inline-block;
            width: 800px !important;
            box-sizing: border-box;
        }
        .w-40 {
            width: 320px;
            box-sizing: border-box;
        }
        .w-60 {
            width: 480px;
            box-sizing: border-box;
        }
        .d-inline-block {
            display: inline-block;
        }


        #created-invoice-qr {
            width: 140px !important;
        }

        #created-invoice-qr img {
            width: 130px;
            height: 130px;
        }

        .clearfix {
            *zoom: 1;
        }

        .clearfix:before, .clearfix:after {
            display: table;
            content: "";
        }

        .clearfix:after {
            clear: both;
        }

        .float-left {
            float: left;

        }

        .h4, h4 {
            width: 100%;
            font-weight: bold;
            text-align: center;
            margin: 0px;
            padding: 5px 0px;
            font-size: 14px;
        }

        .px-5 {
            padding-left: 5px;
            padding-right: 5px;
        }
        .br {
            border-right: 1px solid #eee;
        }
        .bt {
            border-top: 1px solid #eee;
        }
        .bl {
            border-left: 1px solid #eee;
        }
        .has-border {
            border: 1px solid #eee;
            border-spacing: 0;
        }
        .mb-2 {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div>
    <table>
        <tr>
            <td colspan="4">
                <h4>
                    O‘ZBEKISTON RESPUBLIKASI QISHLOQ XO‘JALIGI VAZIRLIGI HUZURIDAGI AGROSANOAT MAJMUI USTIDAN NAZORAT QILISH INSPEKSIYASI TO'LOV VARAG'i
                </h4>
            </td>
        </tr>
        <tr>
            <td rowspan="12" width="300px" class="text-center">
                @include('admin.invoices.images.logo')
                <br>
                <p>Elektron to'lov tizimi</p>
                <div class="mt-4 mb-2">
                    @include('admin.invoices.images.paynet')
                </div>
                <div class="mb-2">
                    @include('admin.invoices.images.payme')
                </div>
                <div class="mb-2">
                    @include('admin.invoices.images.click')
                </div>
            </td>
            <td colspan="3" width="650px" class="text-center"><h3>Invoys {{$invoice->unique_id}}</h3></td>
        </tr>

        <tr>
            <td colspan="3" class="text-center"><b>Moliya vazirligi g'aznachiligi</b></td>
        </tr>
        <tr>
            <td>Markaziy bank Toshkent sh. BB XKKM x/r</td>
            <td colspan="2">23402 000 300 100 001 010</td>
        </tr>
        <tr>
            <td>MFO</td>
            <td colspan="2">00014</td>
        </tr>
        <tr>
            <td>STIR</td>
            <td colspan="2">201 122 919</td>
        </tr>
        <tr>
            <td colspan="2" class="text-center">
                <b>O‘ZBEKISTON RESPUBLIKASI QISHLOQ XO‘JALIGI VAZIRLIGI HUZURIDAGI AGROSANOAT MAJMUI USTIDAN NAZORAT QILISH INSPEKSIYASI</b>
            </td>
            <td rowspan="4" class="text-center">
                <img style="display: inline-block; max-width: 140px;" src="{{$invoice->base64QR}}">
            </td>
        </tr>
        <tr>
            <td>
                Oluvchining hisob raqami
            </td>
            <td>{{optional($invoice->merchant)->account}}</td>
        </tr>
        <tr>
            <td>MFO</td>
            <td>{{INVOICE_RECEIVER_MFO}}</td>
        </tr>
        <tr>
            <td>STIR</td>
            <td>{{INVOICE_RECEIVER_TIN}}</td>
        </tr>
        <tr>
            <td>To'lovchi nomi</td>
            <td colspan="2">{{optional($invoice->customer)->full_name}}</td>
        </tr>
        <tr>
            <td>JS SHIR / STIR</td>
            <td colspan="2">{{$invoice->customer->pinfl ?: $invoice->customer->id_number ?: $invoice->customer->inn }}</td>
        </tr>
        <tr>

            <td>To'lov maqsadi</td>
            <td colspan="2">{{$invoice->paymentType->name}}</td>
        </tr>
        <tr>
            <td></td>
            <td>To'lov miqdori</td>
            <td>{{$invoice->pretty_amount}} so'm</td>
            <td>{{\App\Helpers\NumberHelper::toWords($invoice->amount / 100)}} so'm</td>
        </tr>
        <tr>
            <td></td>
            <td>To'lov varag'i berilgan muddat</td>
            <td>{{$invoice->created_at->format('d-m-Y')}} y</td>
            <td>{{$invoice->created_at->format('H:i:s')}}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-center">
                Ushbu kvitansiya bo'yicha to'lov <span id="invoice-expiration">{{now()->addDays(30)->format('d-m-Y')}} y.</span>
                qadar amalga oshirilishi lozim. Aks holda qaytadan yangi kvitansiya olish talab etiladi
            </td>
        </tr>
    </table>
</div>
</body>
</html>
