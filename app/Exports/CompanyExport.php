<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CompanyExport implements FromCollection, WithHeadings, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            ['PAXTA TOLASINI SERTIFIKATLASHTIRISH AVTOMATLASHTIRILGAN AXBOROT TIZIMI'],
            [
                "Zavod kodi",
                "Buyurtmachi tashkilot nomi",
                "Kip sonni",
                "Netto massai",
            ],
        ];
    }

    public function collection()
    {
        $data = collect($this->data);
        $firstRow = [];

        return collect([$firstRow])->concat($data->map(function ($company) {
            return [
                $company->kod,
                $company->name,
                $company->kip,
                ($company->netto)?round(($company->netto/1000),4) : '',
            ];
        })
    );
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = count($this->data) + 2;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->mergeCells('A1:D1');

        $sheet->setCellValue('A1', 'PAXTA TOLASINI SERTIFIKATLASHTIRISH AVTOMATLASHTIRILGAN AXBOROT TIZIMI');

        $sheet->setCellValue('A2', 'Zavod kodi');
        $sheet->setCellValue('B2', 'Buyurtmachi tashkilot nomi');
        $sheet->setCellValue('C2', 'Kip sonni');
        $sheet->setCellValue('D2', 'Netto massai');

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        $sheet->getStyle('A1:D1')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A1:D1')->getFill()->getStartColor()->setARGB('FFFF00');

        $sheet->getStyle("A1:D{$totalRows}")->applyFromArray($styleArray);

        $sheet->getStyle("A1:D{$totalRows}")->getAlignment()->setHorizontal('center')->setVertical('center');

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(25);

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(40);

        $sheet->getDefaultRowDimension()->setRowHeight(20);
    }

}
