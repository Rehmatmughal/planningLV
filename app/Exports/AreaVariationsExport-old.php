<?php

namespace App\Exports;

use App\Models\AreaVariation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AreaVariationsExport implements FromCollection, WithHeadings
{
    /**
     * Export complete Area Variations data.
     */
    public function collection()
    {
        return AreaVariation::with([
            'plot.project',
            'plot.block',
            'plot.street',
            'plot.size',
            'plot.propertyType',
        ])
        ->get()
        ->map(function ($areaVariation) {

            $plot = $areaVariation->plot;

            return [

                'Project' =>
                    $plot?->project?->project_name,

                'Block' =>
                    $plot?->block?->block_name,

                'Street' =>
                    $plot?->street?->street_name,

                'Plot Number' =>
                    $plot?->plot_number,

                'Property Type' =>
                    $plot?->propertyType?->name,

                'Size' =>
                    $plot?->size?->size_title,

                'Previous Area' =>
                    $areaVariation->previous_area,

                'Measured Area' =>
                    $areaVariation->measured_area,

                'Area Difference' =>
                    ($areaVariation->measured_area ?? 0)
                    - ($areaVariation->previous_area ?? 0),

                'Possession Status' =>
                    $areaVariation->possession_status,

                'LOP Status' =>
                    $areaVariation->lop_status_at_time,

                'Road Status' =>
                    $areaVariation->road_status_at_time,

                'Sewer Status' =>
                    $areaVariation->sewer_status_at_time,

                'Mortgage Status' =>
                    $areaVariation->mortgage_status_at_time,

                'Overall Status' =>
                    $areaVariation->overall_status_at_time,

                'Measured By' =>
                    optional(
                        \App\Models\User::find(
                            $areaVariation->measured_by
                        )
                    )->name,

                'Measured Date' =>
                    $areaVariation->measured_date
                        ? $areaVariation->measured_date->format('d-m-Y')
                        : null,

                'Remarks' =>
                    $areaVariation->remarks,

                'Source' =>
                    $areaVariation->source,

                'Workflow Status' =>
                    match ((int) $areaVariation->workflow_status) {
                        1 => 'Pending',
                        2 => 'Ready for Print',
                        3 => 'Printed',
                        default => $areaVariation->workflow_status,
                    },
            ];
        });
    }

    /**
     * Excel headings.
     */
    public function headings(): array
    {
        return [
            'Project',
            'Block',
            'Street',
            'Plot Number',
            'Property Type',
            'Size',
            'Previous Area',
            'Measured Area',
            'Area Difference',
            'Possession Status',
            'LOP Status',
            'Road Status',
            'Sewer Status',
            'Mortgage Status',
            'Overall Status',
            'Measured By',
            'Measured Date',
            'Remarks',
            'Source',
            'Workflow Status',
        ];
    }
}
