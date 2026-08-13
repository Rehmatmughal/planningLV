<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;

class PlotVariationTemplateExport implements WithEvents
{
    protected $plot;
    protected $variation;

    public function __construct($plot,$variation)
    {
        $this->plot = $plot;
        $this->variation = $variation;
    }

    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => function (BeforeWriting $event) {

                $template = storage_path('app/templates/plot_report_template.xlsx');

                $spreadsheet = IOFactory::load($template);

                $sheet = $spreadsheet->getActiveSheet();

                $plot = $this->plot;
                $av   = $this->variation;

                $sheet->setCellValue('B4',$plot->project->project_name);
                $sheet->setCellValue('B5',$plot->block->block_name);
                $sheet->setCellValue('B6',$plot->street->street_name);
                $sheet->setCellValue('B7',$plot->plot_number);
                $sheet->setCellValue('B8',$plot->size->title);

                $sheet->setCellValue('B10',$plot->lopStatus->lop_status ?? 'N/A');
                $sheet->setCellValue('B11',$plot->mortgageStatus->is_mortgaged ?? 'N/A');

                $sheet->setCellValue('B13',$plot->developmentStatus->sewer_manholes ?? 'N/A');
                $sheet->setCellValue('B14',$plot->developmentStatus->asphalt_tst ?? 'N/A');

                $sheet->setCellValue('B16',$av->measured_date);
                $sheet->setCellValue('B17',$av->measured_area);
                $sheet->setCellValue('B18',$av->road_status_at_time);
                $sheet->setCellValue('B19',$av->sewer_status_at_time);
                $sheet->setCellValue('B20',$av->lop_status_at_time);
                $sheet->setCellValue('B21',$av->remarks);

                $event->writer->reopen($spreadsheet);

            }

        ];
    }
}
// old method
// namespace App\Exports;

// use PhpOffice\PhpSpreadsheet\IOFactory;
// use Maatwebsite\Excel\Concerns\WithEvents;
// use Maatwebsite\Excel\Events\BeforeExport;

// class PlotVariationTemplateExport implements WithEvents
// {
//     protected $plot;
//     protected $variation;

//     public function __construct($plot,$variation)
//     {
//         $this->plot = $plot;
//         $this->variation = $variation;
//     }

//     public function registerEvents(): array
//     {
//         return [

//             BeforeExport::class => function (BeforeExport $event) {

//                 $template = storage_path('app/templates/plot_report_template.xlsx');

//                 $spreadsheet = IOFactory::load($template);

//                 $sheet = $spreadsheet->getActiveSheet();

//                 $plot = $this->plot;
//                 $av   = $this->variation;

//                 $sheet->setCellValue('B4',$plot->project->project_name);
//                 $sheet->setCellValue('B5',$plot->block->block_name);
//                 $sheet->setCellValue('B6',$plot->street->street_name);
//                 $sheet->setCellValue('B7',$plot->plot_number);
//                 $sheet->setCellValue('B8',$plot->size->title);

//                 $sheet->setCellValue('B10',$plot->lopStatus->lop_status ?? 'N/A');
//                 $sheet->setCellValue('B11',$plot->mortgageStatus->is_mortgaged ?? 'N/A');

//                 $sheet->setCellValue('B13',$plot->developmentStatus->sewer_manholes ?? 'N/A');
//                 $sheet->setCellValue('B14',$plot->developmentStatus->asphalt_tst ?? 'N/A');

//                 $sheet->setCellValue('B16',$av->measured_date);
//                 $sheet->setCellValue('B17',$av->measured_area);
//                 $sheet->setCellValue('B18',$av->road_status_at_time);
//                 $sheet->setCellValue('B19',$av->sewer_status_at_time);
//                 $sheet->setCellValue('B20',$av->lop_status_at_time);
//                 $sheet->setCellValue('B21',$av->remarks);

//                 $event->writer->setSpreadsheet($spreadsheet);

//             }

//         ];
//     }
// }
