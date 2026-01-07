@extends('layouts.app')

@section('styles')
 <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/report/company_report.css') }}">
@endsection

@section('content')

@can('viewAny', \App\Models\User::class)

<div class="report-container">

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fe fe-life-buoy mr-1"></i>
                {{ __("message.Korxonalar kesimda ma'lumot") }}
            </li>
        </ol>
    </div>

    {{-- FLASH MESSAGE --}}
    @if (session('message'))
        <div class="alert alert-success text-center">
            <i class="fa fa-check-circle mr-2"></i>
            {{ trans('app.' . str_replace(' ', '_', session('message'))) }}
        </div>
    @endif

    {{-- FILTER COMPONENT --}}
    <x-filter :crop="$crop" :city="$city" :from="$from" :till="$till" />

    {{-- ACTION BUTTONS --}}
    <div class="action-buttons">
        <button onclick="printTable()" class="btn btn-primary">
            <i class="fa fa-print"></i>
            {{ trans('app.Chop etish') }}
        </button>

        <button onclick="exportToExcel()" class="btn btn-success text-white">
            <i class="fa fa-file-excel-o"></i>
            {{ trans('app.Excel fayl') }} (JS)
        </button>

        {{-- <a class="btn btn-success text-white"
           href="{{ route('export.company', request()->only('from','till','city','crop')) }}">
            <i class="fa fa-download"></i>
            {{ trans('app.Excel fayl') }} (Server)
        </a> --}}
    </div>

    {{-- TABLE CARD --}}
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover myTable">
                    <thead>
                        <tr>
                            <th style="width: 4%">#</th>
                            <th>{{ trans('app.Zavod kodi') }}</th>
                            <th>{{ trans('app.Buyurtmachi tashkilot nomi') }}</th>
                            <th class="text-center">{{ trans('app.Kip soni') }}</th>
                            <th class="text-right">{{ trans('app.Massasi') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $offset = (request()->get('page', 1) - 1) * 50;
                        @endphp
                        @forelse ($companies as $company)
                            <tr>
                                <td>{{ $offset + $loop->iteration }}</td>
                                <td><strong>{{ $company->kod }}</strong></td>
                                <td>
                                    <a href="{{ url('/organization/view/' . $company->id) }}">
                                        {{ $company->name }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $company->kip }}</span>
                                </td>
                                <td class="text-right number-cell">
                                    {{ number_format($company->netto / 1000, 4, '.', ',') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fa fa-inbox fa-2x mb-2"></i>
                                    <p>{{ trans('app.Ma\'lumot topilmadi') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                @if($companies->hasPages())
                    <div class="mt-3">
                        {{ $companies->links() }}
                    </div>
                @endif
            </div>

            {{-- TOTALS --}}
            @if($companies->count() > 0)
                <div class="totals-bar">
                    <span>
                        <small>Jami kip soni</small>
                        <strong>{{ number_format($kipTotal) }}</strong>
                    </span>
                    <span>
                        <small>Jami Massasi</small>
                        <strong>{{ $nettoTotal }}</strong>
                    </span>
                </div>
            @endif

        </div>
    </div>

</div>

@else
<div class="report-container">
    <div class="card">
        <div class="card-body unauthorized-card">
            <i class="fa fa-exclamation-circle"></i>
            <p class="text-danger mb-0">
                {{ trans('app.You Are Not Authorize This page.') }}
            </p>
        </div>
    </div>
</div>
@endcan

{{-- PRINT & EXPORT SCRIPTS --}}
<script>
    function printTable() {
        const table = document.querySelector('.myTable');
        if (!table) return;

        const tableHtml = table.outerHTML;
        const totalsBar = document.querySelector('.totals-bar');
        const totalsHtml = totalsBar ? totalsBar.outerHTML : '';

        const printWindow = window.open('', '', 'height=700,width=1000');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>{{ __("message.Korxonalar kesimda ma'lumot") }}</title>
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
                <style>
                    @page {
                        margin: 20mm;
                    }
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                    }
                    .print-header {
                        text-align: center;
                        margin-bottom: 20px;
                        padding-bottom: 10px;
                        border-bottom: 2px solid #333;
                    }
                    .print-header h2 {
                        margin: 0;
                        font-size: 18px;
                        font-weight: bold;
                    }
                    .print-date {
                        text-align: right;
                        margin-bottom: 10px;
                        font-size: 10px;
                        color: #666;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    th, td {
                        border: 1px solid #333;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background: #f0f0f0;
                        font-weight: bold;
                        font-size: 11px;
                    }
                    .text-right { text-align: right; }
                    .text-center { text-align: center; }
                    .totals-bar {
                        background: #f0f0f0;
                        padding: 15px;
                        margin-top: 20px;
                        border: 2px solid #333;
                        display: flex;
                        justify-content: space-around;
                    }
                    .badge { display: none; }
                    a { color: #000; text-decoration: none; }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <h2>{{ __("message.Korxonalar kesimda ma'lumot") }}</h2>
                </div>
                <div class="print-date">
                    {{ trans('app.Sana') }}: ${new Date().toLocaleDateString('uz-UZ')}
                </div>
                ${tableHtml}
                ${totalsHtml}
            </body>
            </html>
        `);

        printWindow.document.close();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 250);
    }

    function exportToExcel() {
        // Get table data
        const table = document.querySelector('.myTable');
        if (!table) {
            alert('Jadval topilmadi!');
            return;
        }

        // Create workbook
        const wb = XLSX.utils.book_new();
        
        // Prepare data array
        const data = [];
        
        // Add header row
        const headers = [];
        table.querySelectorAll('thead th').forEach(th => {
            headers.push(th.textContent.trim());
        });
        data.push(headers);
        
        // Add data rows
        table.querySelectorAll('tbody tr').forEach(tr => {
            const row = [];
            tr.querySelectorAll('td').forEach((td, index) => {
                let value = td.textContent.trim();
                
                // Remove badge styling for Kip soni column
                if (td.querySelector('.badge')) {
                    value = td.querySelector('.badge').textContent.trim();
                }
                
                // Clean number formatting
                if (index === 4) { // Massasi column
                    value = value.replace(/,/g, '');
                }
                
                row.push(value);
            });
            
            // Skip empty rows
            if (row.some(cell => cell !== '')) {
                data.push(row);
            }
        });
        
        // Add totals if available
        const totalsBar = document.querySelector('.totals-bar');
        if (totalsBar) {
            data.push([]); // Empty row
            const totals = totalsBar.querySelectorAll('span');
            totals.forEach(span => {
                const text = span.textContent.trim().replace(/\s+/g, ' ');
                data.push([text]);
            });
        }
        
        // Create worksheet
        const ws = XLSX.utils.aoa_to_sheet(data);
        
        // Set column widths
        ws['!cols'] = [
            { wch: 5 },   // #
            { wch: 15 },  // Zavod kodi
            { wch: 40 },  // Tashkilot nomi
            { wch: 12 },  // Kip soni
            { wch: 15 }   // Massasi
        ];
        
        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, "Korxonalar");
        
        // Generate filename with date
        const date = new Date().toISOString().split('T')[0];
        const filename = `korxonalar_hisobot_${date}.xlsx`;
        
        // Save file
        XLSX.writeFile(wb, filename);
    }
</script>

@endsection