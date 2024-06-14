<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class PreparedExport implements FromCollection, WithHeadings, WithStyles
{
    protected $prepareds;
    protected $totalRows;

    public function __construct($prepareds)
    {
        $this->prepareds = $prepareds;
    }

    public function headings(): array
    {
        return [
            ['PAXTA TOLASINI SERTIFIKATLASHTIRISH AVTOMATLASHTIRILGAN AXBOROT TIZIMI'],
            [
                '',
                trans('app.Toʼda (partiya) raqami'),
                trans("app.Bo'lak partiya"),
                trans('app.Sertifikat reestr raqami'),
                trans('app.Sertifikat sanasi'),
                trans('app.Sort'),
                trans('app.Sinf'),
                trans('app.Kip soni'),
                trans("app.Jami og'irlik(kg)"),
                trans("app.Sof Og'irlik(kg)")
            ],
        ];
    }

    public function collection()
    {
        $data = collect($this->prepareds);

        $collection = collect([]);

        foreach ($data as $key => $prepared) {
            foreach ($prepared as $inside_key => $box) {
                $collection->push([
                    '',
                    'header' => "{$inside_key} {$key}", '', '', '', '', '', '', '', '', '',
                ]);

                foreach ($box as $item) {
                    $collection->push([
                        '',
                        optional($item->dalolatnoma->test_program->application->crops)->party_number,
                        $this->getPartNumber($item),
                        optional($item->certificate)->reestr_number,
                        Carbon::parse(optional($item->certificate)->given_date)->format('d.m.Y'),
                        $item->sort,
                        $item->generation->name,
                        $item->count,
                        optional($item)->amount ? $item->amount . ' ' : '',
                        $item->amount != null ? $item->amount - $item->count * optional($item->dalolatnoma)->tara : '',
                    ]);
                }
            }
        }

        // // Calculate totals
        // $totalKip = $data->flatten()->sum('count');
        $totalAmount = $data->flatten()->sum('amount');
        // $totalNetWeight = $data->flatten()->sum(function ($item) {
        //     return $item->amount != null ? $item->amount - $item->count * optional($item->dalolatnoma)->tara : 0;
        // });

        // // Add totals row
        $totalsRow = [
            '',
            trans("app.Jami og'irlik(kg)"),
            '',
            '',
            '',
            '',
            '',
            '',
        //     $totalKip,
            $totalAmount ? $totalAmount . ' ' : '',
        //     $totalNetWeight,
        ];

        $collection->push($totalsRow);

        $this->totalRows = $collection->count() + 2;

        return $collection;
    }

    private function getPartNumber($item)
    {
        static $son = 0;
        static $check = 0;

        if ($check == optional($item->dalolatnoma->test_program->application->crops)->party_number) {
            return ++$son;
        } else {
            $check = optional($item->dalolatnoma->test_program->application->crops)->party_number;
            $son = 0;
            return $son;
        }
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = $this->totalRows;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $sheet->mergeCells('A1:J1');

        $sheet->setCellValue('A1', 'PAXTA TOLASINI SERTIFIKATLASHTIRISH AVTOMATLASHTIRILGAN AXBOROT TIZIMI');

        $sheet->getStyle('A1:J2')->getFont()->setBold(true);
        $sheet->getStyle("A{$totalRows}:J{$totalRows}")->getFont()->setBold(true);

        $sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A1:J1')->getFill()->getStartColor()->setARGB('FFFF00');

        $sheet->getStyle("A1:J{$totalRows}")->applyFromArray($styleArray);

        $sheet->getStyle("A1:J{$totalRows}")->getAlignment()->setHorizontal('center')->setVertical('center');

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(25);

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(45);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(25);
        $sheet->getColumnDimension('J')->setWidth(25);

        $sheet->getDefaultRowDimension()->setRowHeight(20);
    }
}
