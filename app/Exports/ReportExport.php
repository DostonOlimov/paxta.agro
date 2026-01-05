<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportExport implements FromCollection, WithHeadings, WithStyles, WithEvents, ShouldAutoSize
{
    protected $query;
    protected $chunkSize = 500;

    public function __construct($query)
    {
        // Accept query builder instead of collection to enable chunking
        $this->query = $query;
    }

    public function collection()
    {
        $rows = new Collection();
        
        // Process data in chunks to reduce memory usage
        $this->query->chunk($this->chunkSize, function ($results) use ($rows) {
            foreach ($results as $result) {
                $rows->push($this->mapResultToRow($result));
            }
        });
        
        return $rows;
    }

    protected function mapResultToRow($result): array
    {
        return [
            $result->date ?? '',
            $result->dalolatnoma->number ?? '',
            $result->certificate->reestr_number ?? '',
            data_get($result, 'dalolatnoma.test_program.application.organization.city.region.name', ''),
            data_get($result, 'dalolatnoma.test_program.application.organization.city.name', ''),
            data_get($result, 'dalolatnoma.test_program.application.organization.name', ''),
            data_get($result, 'dalolatnoma.test_program.application.prepared.name', ''),
            data_get($result, 'dalolatnoma.test_program.application.crops.name.name', ''),
            data_get($result, 'dalolatnoma.test_program.application.crops.selection.name', ''),
            data_get($result, 'dalolatnoma.test_program.application.crops.party_number', ''),
            data_get($result, 'dalolatnoma.test_program.application.crops.year', ''),
            optional($result->dalolatnoma->akt_amount)->count() ?? '',
            optional($result->dalolatnoma->akt_amount)->sum('amount') ?? '',
            $this->calculateNetWeight($result),
            $result->type ?? '',
            $result->sort ?? '',
            $result->class ?? '',
            $result->staple_length ?? '',
            $result->micronaire ?? '',
            $result->strength ?? '',
            $result->uniformity ?? '',
            $result->moisture ?? '',
        ];
    }

    protected function calculateNetWeight($result)
    {
        $aktAmount = optional($result->dalolatnoma->akt_amount)->sum('amount');
        $tara = $result->dalolatnoma->tara ?? 0;
        
        return $aktAmount ? ($aktAmount - $tara) : '';
    }

    public function headings(): array
    {
        return [
            ['PAXTA TOLASINI SERTIFIKATLASHTIRISH AVTOMATLASHTIRILGAN AXBOROT TIZIMI'],
            [
                "Ariza sanasi",
                "Dalolatnoma raqami",
                "Sertifikat reestr raqami",
                "Na'muna olingan viloyat",
                "Na'muna olingan shahar yoki tuman",
                "Buyurtmachi korxona yoki tashkilot nomi",
                "Tayorlangan shaxobcha yoki sexning nomi",
                "Nomi",
                "Seleksiya nomi",
                "Toʼda (partiya) raqami",
                "Hosil yili",
                "To'dadagi toylar soni (dona)",
                "Jami og'irlik (kg)",
                "Sof og'irlik (kg)",
                "Tip",
                "Sort",
                "Sinf",
                "Shtaple uzunligi",
                "Mikroneyr",
                "Solishtirma uzunlik kuchi",
                "Uzunligi bo'yicha bir xillik ko'rsatkichi (%)",
                "Namlik ko'rsatkichi (%)",
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount = $this->query->count();
        $totalRows = $rowCount + 3; // +3 for header rows

        // Apply borders to all cells
        $sheet->getStyle("A1:V{$totalRows}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Merge header cells
        $this->mergeHeaderCells($sheet);

        // Apply header styling
        $this->styleHeaders($sheet);

        // Set dimensions
        $this->setDimensions($sheet);

        return [];
    }

    protected function mergeHeaderCells(Worksheet $sheet): void
    {
        // Title row
        $sheet->mergeCells('A1:V1');

        // Main headers (rows 2-3)
        $singleColumnHeaders = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
        foreach ($singleColumnHeaders as $col) {
            $sheet->mergeCells("{$col}2:{$col}3");
        }

        // Quality control section header
        $sheet->mergeCells('O2:V2');
    }

    protected function styleHeaders(Worksheet $sheet): void
    {
        // Title styling
        $sheet->getStyle('A1:V1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFF00'],
            ],
        ]);

        // Header rows styling
        $sheet->getStyle('A2:V3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '33A2FF'],
            ],
        ]);

        // Set header values
        $this->setHeaderValues($sheet);
    }

    protected function setHeaderValues(Worksheet $sheet): void
    {
        $sheet->setCellValue('O2', 'Sifat nazorati natijalari');
        
        $headers = [
            'O3' => 'Tip',
            'P3' => 'Sort',
            'Q3' => 'Sinf',
            'R3' => 'Shtaple uzunligi',
            'S3' => 'Mikroneyr',
            'T3' => 'Solishtirma uzunlik kuchi',
            'U3' => "Uzunligi bo'yicha bir xillik ko'rsatkichi, %",
            'V3' => "Namlik ko'rsatkichi, %",
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
    }

    protected function setDimensions(Worksheet $sheet): void
    {
        // Row heights
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(25);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getDefaultRowDimension()->setRowHeight(20);

        // Column widths
        $columnWidths = [
            'A' => 15, 'B' => 25, 'C' => 30, 'D' => 40, 'E' => 40,
            'F' => 50, 'G' => 50, 'H' => 30, 'I' => 30, 'J' => 30,
            'K' => 20, 'L' => 40, 'M' => 25, 'N' => 20, 'O' => 20,
            'P' => 25, 'Q' => 25, 'R' => 25, 'S' => 30, 'T' => 35,
            'U' => 50, 'V' => 30,
        ];

        foreach ($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Additional formatting can be added here if needed
            },
        ];
    }
}