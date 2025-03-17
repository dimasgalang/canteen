<?php

namespace App\Exports;

use App\Models\Canteen;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;

class CanteensExport implements WithHeadings, WithStrictNullComparison, WithEvents, WithMapping, WithStyles, FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */

    use Exportable;
    protected $fromdate;
    protected $todate;
    protected $canteen_no;

    function __construct($fromdate, $todate, $canteen_no)
    {
        $this->fromdate = $fromdate;
        $this->todate = $todate;
        $this->canteen_no = $canteen_no;
    }

    public function collection()
    {
        return Canteen::select('*')->where('date', '>=', $this->fromdate)->where('date', '<=', $this->todate)->where('canteen_no', '=', $this->canteen_no)->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'NPK',
            'Nama Karyawan',
            'Kantin',
            'Tanggal',
            'Waktu Scanning',
        ];
    }
    public function map($canteens): array
    {
        return [
            $canteens->row_num,
            $canteens->npk,
            $canteens->name,
            $canteens->canteen_no,
            $canteens->date,
            $canteens->created_at,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray(
            [
                'font' => [
                    'name' => 'Arial',
                    'bold' => true,
                    'italic' => false,
                    'underline' => false,
                    'strikethrough' => false,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => [
                            'rgb' => '000000'
                        ]
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => false,
                ],
                'quotePrefix'    => true
            ]
        );
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // $event->sheet->getDefaultRowDimension()->setRowHeight(100);
                // $event->sheet->getRowDimension(1)->setRowHeight(10);
                // $event->sheet->getColumnDimension('L')->setWidth(100);
                $workSheet = $event->sheet->getDelegate();

                $this->collection()->each(function ($orderMaster, $index) use ($workSheet) {
                    $index += 2;
                    $workSheet->getColumnDimension('A')->setWidth(8);
                    $workSheet->getColumnDimension('B')->setWidth(10);
                    $workSheet->getColumnDimension('C')->setWidth(20);
                    $workSheet->getColumnDimension('D')->setWidth(10);
                    $workSheet->getColumnDimension('E')->setWidth(15);
                    $workSheet->getColumnDimension('F')->setWidth(20);

                    $workSheet->getStyle("A$index:F$index")->applyFromArray(
                        [
                            'font' => [
                                'name' => 'Arial',
                                'bold' => false,
                                'italic' => false,
                                'underline' => false,
                                'strikethrough' => false,
                                'color' => [
                                    'rgb' => '000000'
                                ]
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => [
                                        'rgb' => '000000'
                                    ]
                                ],
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                                'wrapText' => false,
                            ],
                            'quotePrefix'    => true
                        ]
                    );
                });
            },
        ];
    }
}
