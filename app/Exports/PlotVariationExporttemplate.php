<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;


class PlotVariationExporttemplate implements WithEvents
{
    protected $plot;
    protected $av;

    public function __construct($plot, $av)
    {
        $this->plot = $plot;
        $this->av = $av;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('B3', $this->plot->project->project_name);
                $sheet->setCellValue('D3', $this->plot->block->block_name);

                $sheet->setCellValue('B4', $this->plot->street->street_name);
                $sheet->setCellValue('D4', $this->plot->plot_number);

                $sheet->setCellValue('B8', $this->av->measured_date);
                $sheet->setCellValue('D8', $this->av->measured_area);
            }
        ];
    }
    // public function registerEvents(): array
    // {
    //     return [

    //         // ✅ STEP 4 yahan likhna hai
    //         BeforeExport::class => function(BeforeExport $event) {
    //             $event->writer->reopen(
    //                 storage_path('app/templates/template.xlsx'),
    //                 \Maatwebsite\Excel\Excel::XLSX
    //             );

    //             $event->writer->getSheetByIndex(0);
    //         },

    //         // ✅ Data fill yahan hoga
    //         AfterSheet::class => function(AfterSheet $event) {

    //             $sheet = $event->sheet->getDelegate();

    //             $sheet->setCellValue('B3', $this->plot->project->project_name);
    //             $sheet->setCellValue('D3', $this->plot->block->block_name);

    //             $sheet->setCellValue('B4', $this->plot->street->street_name);
    //             $sheet->setCellValue('D4', $this->plot->plot_number);

    //             $sheet->setCellValue('B5', $this->plot->size->title);
    //             $sheet->setCellValue('D5', $this->av->lop_status_at_time ?? 'N/A');

    //             $sheet->setCellValue('B8', $this->av->measured_date);
    //             $sheet->setCellValue('D8', $this->av->measured_area);
    //         }
    //     ];
    // }
}