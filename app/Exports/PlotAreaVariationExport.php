<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class PlotAreaVariationExport implements FromArray
{
    protected $plot;
    protected $variation;

    public function __construct($plot,$variation)
    {
        $this->plot = $plot;
        $this->variation = $variation;
    }

    public function array(): array
    {
        $plot = $this->plot;
        $av = $this->variation;

        return [

            ['Project',$plot->project->project_name],
            ['Block',$plot->block->block_name],
            ['Street',$plot->street->street_name],
            ['Plot No',$plot->plot_number],
            ['Size',$plot->size->title],

            [],

            ['LOP Status',$plot->lopStatus->lop_status ?? 'N/A'],
            ['Mortgage',$plot->mortgageStatus->is_mortgaged ?? 'N/A'],

            [],

            ['Sewer',$plot->developmentStatus->sewer_manholes ?? 'N/A'],
            ['Asphalt',$plot->developmentStatus->asphalt_tst ?? 'N/A'],

            [],

            ['Area Variation Date',$av->measured_date],
            ['Measured Area',$av->measured_area],
            ['Road Status',$av->road_status_at_time],
            ['Sewer Status',$av->sewer_status_at_time],
            ['LOP Status',$av->lop_status_at_time],
            ['Remarks',$av->remarks]

        ];
    }
}