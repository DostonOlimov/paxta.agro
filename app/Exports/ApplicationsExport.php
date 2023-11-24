<?php
//
//namespace App\Exports;
//
//use Illuminate\Support\Facades\DB;
//use Maatwebsite\Excel\Concerns\FromCollection;
//use Maatwebsite\Excel\Concerns\WithHeadings;
//use Maatwebsite\Excel\Concerns\WithStyles;
//use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
//
//class ApplicationsExport implements FromCollection,WithHeadings,WithStyles
//{
//    protected $applications;
//
//    public function __construct($applications)
//    {
//        $this->applications = $applications;
//    }
//
//    /**
//     * @return \Illuminate\Support\Collection
//     */
//    public function headings():array{
//        return[
//            ['QISHLOQ XO‘JALIK EKINLARI URUG‘LARINI SERTIFIKATLASHTIRISH AVTOMATLASHTIRILGAN AXBOROT TIZIMI UMUMIY HISOBOTI'],
//            [
//                'Ariza raqami',
//                'Ariza sanasi',
//                'Na\'muna olingan viloyat',
//                'Na\'muna olingan shahar yoki tuman',
//                'Buyurtmachi korxona yoki tashkilot nomi',
//                'Urugʼlik tayorlangan shaxobcha yoki sex nomi',
//                'Ishlab chiqargan davlat',
//                'Urug\'lik turi',
//                'Urug\'lik navi',
//                'Urug\'lik avlodi',
//                'Toʼda (partiya) raqami',
//                'Urug\'lik miqdori',
//                'Hosil yili',
//                'Sinov bayonnoma raqami',
//                'Sertifikat',
//                '',
//                'Tahlil natija',
//                '',
//                '',
//                'Papka raqami',
//                'Izoh'
//            ],
//            [
//                '',
//                '',
//                '',
//                '',
//                '',
//                '',
//                '',
//                '',
//                '',
//                '',
//                '',
//                '',
//                '',
//                '',
//                'Reestr raqami',
//                'Berilgan sanasi',
//                'Raqami',
//                'Berilgan sanasi',
//                'Yaroqliligi',
//                '',
//                ''
//            ]
//
//        ];
//    }
//    public function collection()
//    {
//        $data = DB::table('applications')
//            ->join('users', 'users.id', '=', 'employees_summa.user_id')
//            ->join('work_zones', 'users.work_zone_id', '=', 'work_zones.id')
//            ->leftJoin('employee_days', function ($join) {
//                $join->on('employee_days.user_id', '=', 'employees_summa.user_id')
//                    ->where('employee_days.month_id', '=', 4);
//            })
//            ->where('employees_summa.month', '=', 4)
//            ->select([
//                'users.first_name',
//                'users.last_name',
//                'users.father_name',
//                'users.lavozimi',
//                'work_zones.name as yunalishi',
//                'employee_days.month_id',
//                'users.salary',
//                'employee_days.days as ish_kuni',
//                'employees_summa.rating',
//                'employees_summa.current_ball',
//                'employees_summa.ustama',
//                'employees_summa.created_at',
//                'employees_summa.total_summa',
//                'employees_summa.active_summa',
//                'employees_summa.foiz',
//                'employees_summa.new_ustama',
//                'employees_summa.updated_at',
//                'employees_summa.new_total',
//
//
//            ])
//            ->get();
//        return $this->applications;
//    }
//    public function styles(Worksheet $sheet)
//    {
//        $sheet->mergeCells('A1:U1');
//        $sheet->mergeCells('O2:P2');
//        $sheet->mergeCells('Q2:S2');
//
//        $sheet->mergeCells('A2:A3');
//        $sheet->mergeCells('B2:B3');
//        $sheet->mergeCells('C2:C3');
//        $sheet->mergeCells('D2:D3');
//        $sheet->mergeCells('E2:E3');
//        $sheet->mergeCells('F2:F3');
//        $sheet->mergeCells('G2:G3');
//        $sheet->mergeCells('H2:H3');
//        $sheet->mergeCells('I2:I3');
//        $sheet->mergeCells('J2:J3');
//        $sheet->mergeCells('K2:K3');
//        $sheet->mergeCells('L2:L3');
//        $sheet->mergeCells('M2:M3');
//        $sheet->mergeCells('N2:N3');
//
//        $sheet->mergeCells('T2:T3');
//        $sheet->mergeCells('U2:U3');
//
//        $sheet->getStyle('A1:U2')->getFont()->setBold(true);
//        $sheet->getStyle('A1:C100')->getAlignment()->setHorizontal('center');
//        $sheet->getStyle('F3:S100')->getAlignment()->setHorizontal('center');
//        $sheet->getRowDimension(1)->setRowHeight(50);
//        $sheet->getRowDimension(2)->setRowHeight(15);
//        $sheet->getRowDimension(2)->setRowHeight(15);
//
//        $sheet->getColumnDimension('A')->setWidth(15);
//        $sheet->getColumnDimension('B')->setWidth(15);
//        $sheet->getColumnDimension('C')->setWidth(20);
//        $sheet->getColumnDimension('D')->setWidth(20);
//        $sheet->getColumnDimension('E')->setWidth(60);
//        $sheet->getColumnDimension('F')->setWidth(15);
//        $sheet->getColumnDimension('G')->setWidth(15);
//        $sheet->getColumnDimension('H')->setWidth(15);
//        $sheet->getColumnDimension('I')->setWidth(10);
//        $sheet->getColumnDimension('J')->setWidth(10);
//        $sheet->getColumnDimension('K')->setWidth(30);
//        $sheet->getColumnDimension('L')->setWidth(30);
//        $sheet->getColumnDimension('M')->setWidth(30);
//        $sheet->getColumnDimension('N')->setWidth(30);
//        $sheet->getColumnDimension('O')->setWidth(30);
//        $sheet->getColumnDimension('P')->setWidth(30);
//        $sheet->getColumnDimension('Q')->setWidth(20);
//        $sheet->getColumnDimension('R')->setWidth(30);
//        $sheet->getColumnDimension('S')->setWidth(30);
//        $sheet->getColumnDimension('T')->setWidth(30);
//        $sheet->getColumnDimension('U')->setWidth(30);
//        $sheet->getDefaultColumnDimension()->setAutoSize(false);
//    }
//}


namespace App\Exports;

use App\Services\LocationService;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ApplicationsExport implements FromView, Responsable, ShouldAutoSize, WithEvents
{
    use Exportable;

    /**
     * Optional Writer Type
     */
    private $fileName = '';

    private $writerType = Excel::XLSX;

    protected $applications;

    public function __construct($applications)
    {
        $this->fileName = LocationService::getRegion((int) 4000)->name . '_' . now()->format(USER_DATE_FORMAT) . '.' . Excel::XLSX;
        $this->applications = $applications;
    }

    public function view(): View
    {
        return view('reports.export_html', [
            'apps' => $this->applications
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:AQ30000')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ]
                ]);
                $event->sheet->getStyle('A1:AQ2')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => Color::COLOR_YELLOW],
                    ],
                ]);
            },
        ];
    }
}
