<?php

namespace App\Exports;

use App\Models\AreaVariation;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AreaVariationsExport implements FromCollection, WithHeadings
{
    /**
     * Export complete Area Variations data.
     */
    public function collection()
    {
        $variations = AreaVariation::with([
            'plot.project',
            'plot.block',
            'plot.street',
            'plot.size',
            'plot.propertyType',
            'plot.category',
            'plot.developmentStatus',
            'plot.possessionStatus',
        ])
        ->orderBy('plot_id')
        ->orderByDesc('measured_date')
        ->get();

        /*
        |--------------------------------------------------------------------------
        | Find latest Area Variation for each plot
        |--------------------------------------------------------------------------
        */

        $latestVariationIds = $variations
            ->groupBy('plot_id')
            ->map(function ($plotVariations) {

                return $plotVariations
                    ->sortByDesc('measured_date')
                    ->first()
                    ?->id;

            })
            ->filter()
            ->values()
            ->flip();

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        $users = User::pluck('name', 'id');

        /*
        |--------------------------------------------------------------------------
        | Prepare Excel rows
        |--------------------------------------------------------------------------
        */

        return $variations->map(function ($variation) use (
            $users,
            $latestVariationIds
        ) {

            $plot = $variation->plot;

            $isLatest = isset(
                $latestVariationIds[$variation->id]
            );

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

                'Category' =>
                    $plot?->category?->category_title,

                'Size' =>
                    $plot?->size?->title,

                'Variation Status' =>
                    $isLatest ? 'Latest' : 'Previous',

                'Previous Area' =>
                    $variation->previous_area,

                'Measured Area' =>
                    $variation->measured_area,

                'Area Difference' =>
                    ($variation->measured_area ?? 0)
                    - ($variation->previous_area ?? 0),

                'Possession Status' =>
                    $plot?->possessionStatus?->possession_status,

                'LOP Status' =>
                    $variation->lop_status_at_time,

                'Road Status' =>
                    $variation->road_status_at_time,

                'Sewer Status' =>
                    $variation->sewer_status_at_time,

                'Mortgage Status' =>
                    $variation->mortgage_status_at_time,

                'Overall Status' =>
                    $plot?->developmentStatus?->overall_status,

                'Measured By' =>
                    $users[$variation->measured_by] ?? 'N/A',

                'Measured Date' =>
                    $variation->measured_date
                        ? $variation->measured_date->format('d-m-Y')
                        : null,

                'Remarks' =>
                    $variation->remarks,

                'Source' =>
                    $variation->source,

                'Workflow Status' =>
                    match ((int) $variation->workflow_status) {
                        1 => 'Pending',
                        2 => 'Ready for Print',
                        3 => 'Printed',
                        default => $variation->workflow_status,
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
            'Category',
            'Size',
            'Variation Status',
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
