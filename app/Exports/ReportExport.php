<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportExport implements FromCollection, WithHeadings, WithStyles
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
                "Ariza sanasi",
                "Sertifikat reestr raqami",
                "Dalolatnoma raqami",
                "Na'muna olingan viloyat",
                "Na'muna olingan shahar yoki tuman",
                "Buyurtmachi korxona yoki tashkilot nomi",
                "Tayorlangan shaxobcha yoki sexning nomi",
                "Ishlab chiqargan davlat",
                "Nomi",
                "Toʼda (partiya) raqami",
                "Miqdori",
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

    public function collection()
    {
        $data = collect($this->data);
        $firstRow = [
            'Sanasi' => '',
        ];

        return collect([$firstRow])->concat($data->map(function ($result) {
            return [
                $result->test_program->application->date ?? 'N/A',
                (optional($result->certificate)->reestr_number)??'',
                optional($result->dalolatnoma)->number ?? 'N/A',
               (isset($result->test_program->application))?  optional($result->test_program->application->organization)->city->region->name ?? 'N/A':'',
               (isset($result->test_program->application))?  optional($result->test_program->application->organization)->city->name ?? 'N/A':'',
               (isset($result->test_program->application))?  optional($result->test_program->application->organization)->name ?? 'N/A':'',
               (isset($result->test_program->application))?  optional($result->test_program->application->prepared)->name ?? 'N/A':'',
               (isset($result->test_program->application))?  optional($result->test_program->application->crops->country)->name ?? 'N/A':'',
               (isset($result->test_program->application))?  optional($result->test_program->application->crops->name)->name ?? 'N/A':'',
               (isset($result->test_program->application))?  optional($result->test_program->application->crops)->party_number ?? 'N/A':'',
                optional($result)->amount ?? 'N/A',
                (isset($result->test_program->application))? optional($result->test_program->application->crops)->year ?? 'N/A':'',
                optional($result)->count ?? 'N/A',
                optional($result)->amount ?? '',
                (optional($result)->amount)?optional($result)->amount - optional($result)->count * optional(optional($result->dalolatnoma->test_program->application)->prepared)->tara ?? 'N/A':'',
                4,
                optional($result)->sort ?? 'N/A',
                optional(\App\Models\CropsGeneration::where('kod','=',$result->class)->first())->name ?? 'N/A',
                round($result->staple) ?? 'N/A',
                round($result->mic, 1) ?? 'N/A',
                round($result->strength, 1) ?? 'N/A',
                round($result->uniform, 1) ?? 'N/A',
                round($result->humidity, 2) ?? 'N/A'
            ];
        })
    );
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = count($this->data) + 3;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->mergeCells('A1:W1');
        $sheet->mergeCells('A2:A3');
        $sheet->mergeCells('B2:B3');
        $sheet->mergeCells('C2:C3');
        $sheet->mergeCells('D2:D3');
        $sheet->mergeCells('E2:E3');
        $sheet->mergeCells('F2:F3');
        $sheet->mergeCells('G2:G3');
        $sheet->mergeCells('H2:H3');
        $sheet->mergeCells('I2:I3');
        $sheet->mergeCells('J2:J3');
        $sheet->mergeCells('K2:K3');
        $sheet->mergeCells('L2:L3');
        $sheet->mergeCells('M2:M3');
        $sheet->mergeCells('N2:N3');
        $sheet->mergeCells('O2:O3');

        $sheet->mergeCells('P2:W2');

        $sheet->setCellValue('A1', 'PAXTA TOLASINI SERTIFIKATLASHTIRISH AVTOMATLASHTIRILGAN AXBOROT TIZIMI');
        $sheet->setCellValue('P2', 'Sifat nazorati natijalari');

        $sheet->getStyle('P2:W3')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('A2', 'Ariza sanasi');
        $sheet->setCellValue('B2', 'Sertifikat reestr raqami');
        $sheet->setCellValue('C2', 'Dalolatnoma raqami');
        $sheet->setCellValue('D2', 'Na\'muna olingan viloyat');
        $sheet->setCellValue('E2', 'Na\'muna olingan shahar yoki tuman');
        $sheet->setCellValue('F2', 'Buyurtmachi korxona yoki tashkilot nomi');
        $sheet->setCellValue('G2', 'Tayorlangan shaxobcha yoki sexning nomi');
        $sheet->setCellValue('H2', 'Ishlab chiqargan davlat');
        $sheet->setCellValue('I2', 'Nomi');
        $sheet->setCellValue('J2', 'Toʼda (partiya) raqami');
        $sheet->setCellValue('K2', 'Miqdori');
        $sheet->setCellValue('L2', 'Hosil yili');
        $sheet->setCellValue('M2', 'To\'dadagi toylar soni (dona)');
        $sheet->setCellValue('N2', 'Jami og\'irlik(kg)');
        $sheet->setCellValue('O2', 'Sof Og\'irlik(kg)');

        $sheet->setCellValue('P3', 'Tip');
        $sheet->setCellValue('Q3', 'Sort');
        $sheet->setCellValue('R3', 'Sinf');
        $sheet->setCellValue('S3', 'Shtaple uzunligi');
        $sheet->setCellValue('T3', 'Mikroneyr');
        $sheet->setCellValue('U3', 'Solishtirma uzunlik kuchi');
        $sheet->setCellValue('V3', 'Uzunligi bo\'yicha bir xillik ko\'rsatkichi, %');
        $sheet->setCellValue('W3', 'Namlik ko\'rsatkichi, %');

        $sheet->getStyle('A2:O2')->getFont()->setBold(true);
        $sheet->getStyle('A1:W1')->getFont()->setBold(true);
        $sheet->getStyle('P2:W2')->getFont()->setBold(true);
        $sheet->getStyle('P3:W3')->getFont()->setBold(true);

        $sheet->getStyle('A1:W1')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A1:W1')->getFill()->getStartColor()->setARGB('FFFF00');

        $sheet->getStyle('A2:W3')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A2:W3')->getFill()->getStartColor()->setARGB('33A2FF');
        $sheet->getStyle("A1:W{$totalRows}")->applyFromArray($styleArray);

        $sheet->getStyle("A1:W{$totalRows}")->getAlignment()->setHorizontal('center')->setVertical('center');

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(25);
        $sheet->getRowDimension(3)->setRowHeight(20);

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->getColumnDimension('F')->setWidth(50);
        $sheet->getColumnDimension('G')->setWidth(50);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(40);
        $sheet->getColumnDimension('N')->setWidth(25);
        $sheet->getColumnDimension('O')->setWidth(20);
        $sheet->getColumnDimension('P')->setWidth(20);
        $sheet->getColumnDimension('Q')->setWidth(25);
        $sheet->getColumnDimension('R')->setWidth(25);
        $sheet->getColumnDimension('S')->setWidth(25);
        $sheet->getColumnDimension('T')->setWidth(30);
        $sheet->getColumnDimension('U')->setWidth(35);
        $sheet->getColumnDimension('V')->setWidth(50);
        $sheet->getColumnDimension('W')->setWidth(30);

        $sheet->getDefaultRowDimension()->setRowHeight(20);
    }

}
