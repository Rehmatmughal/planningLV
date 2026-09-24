<?php

namespace App\Exports;

use App\Models\Plot;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class LopMortgageExport implements FromView, WithEvents
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $query = Plot::with([
            'project',
            'block',
            'street',
            'propertyType',
            'size',
            'lopStatus',
            'mortgageStatus',
        ]);

        // Project
        if (!empty($this->filters['project_id'])) {
            $query->where(
                'project_id',
                $this->filters['project_id']
            );
        }

        // Block
        if (!empty($this->filters['block_id'])) {
            $query->where(
                'block_id',
                $this->filters['block_id']
            );
        }

        // Street
        if (!empty($this->filters['street_id'])) {
            $query->where(
                'street_id',
                $this->filters['street_id']
            );
        }

        // Property Type
        if (!empty($this->filters['property_type_id'])) {
            $query->where(
                'property_type_id',
                $this->filters['property_type_id']
            );
        }

        // LOP Status
        if (!empty($this->filters['lop_status'])) {
            $query->whereHas('lopStatus', function ($q) {
                $q->where(
                    'lop_status',
                    $this->filters['lop_status']
                );
            });
        }

        // Mortgage
        if (!empty($this->filters['is_mortgaged'])) {
            $query->whereHas('mortgageStatus', function ($q) {
                $q->where(
                    'is_mortgaged',
                    $this->filters['is_mortgaged']
                );
            });
        }

        // Plot Number
        if (!empty($this->filters['plot_number'])) {
            $query->where(
                'plot_number',
                'like',
                '%' . $this->filters['plot_number'] . '%'
            );
        }

        $plots = $query
            ->orderBy('project_id')
            ->orderBy('block_id')
            ->orderBy('plot_number')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Project Title
        |--------------------------------------------------------------------------
        */

        $projectNames = $plots
            ->map(fn ($plot) => $plot->project?->project_name)
            ->filter()
            ->unique()
            ->values();

        if ($projectNames->count() === 1) {

            $projectTitle = $projectNames->first();

        } elseif ($projectNames->count() > 1) {

            $projectTitle = $projectNames->implode(' and ');

        } else {

            $projectTitle = 'All Projects';
        }

        /*
        |--------------------------------------------------------------------------
        | Applied Filters
        |--------------------------------------------------------------------------
        */

        $appliedFilters = [];

        // Project
        if (!empty($this->filters['project_id'])) {

            $project = \App\Models\Project::find(
                $this->filters['project_id']
            );

            if ($project) {
                $appliedFilters[] =
                    'Project: ' . $project->project_name;
            }
        }

        // Block
        if (!empty($this->filters['block_id'])) {

            $block = \App\Models\Block::find(
                $this->filters['block_id']
            );

            if ($block) {
                $appliedFilters[] =
                    'Block: ' . $block->block_name;
            }
        }

        // Street
        if (!empty($this->filters['street_id'])) {

            $street = \App\Models\Street::find(
                $this->filters['street_id']
            );

            if ($street) {
                $appliedFilters[] =
                    'Street: ' . $street->street_name;
            }
        }

        // Property Type
        if (!empty($this->filters['property_type_id'])) {

            $propertyType = \App\Models\PropertyType::find(
                $this->filters['property_type_id']
            );

            if ($propertyType) {
                $appliedFilters[] =
                    'Property Type: ' . $propertyType->name;
            }
        }

        // LOP Status
        if (!empty($this->filters['lop_status'])) {

            $appliedFilters[] =
                'LOP: ' .
                ucwords(
                    str_replace(
                        '_',
                        ' ',
                        $this->filters['lop_status']
                    )
                );
        }

        // Mortgage
        if (!empty($this->filters['is_mortgaged'])) {

            $appliedFilters[] =
                'Mortgage: ' .
                ucfirst(
                    $this->filters['is_mortgaged']
                );
        }

        // Plot Number
        if (!empty($this->filters['plot_number'])) {

            $appliedFilters[] =
                'Plot No: ' .
                $this->filters['plot_number'];
        }

        return view('exports.lop_mortgage_excel', [
            'plots' => $plots,
            'projectTitle' => $projectTitle,
            'appliedFilters' => $appliedFilters,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Excel Formatting
    |--------------------------------------------------------------------------
    */

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                /*
                |--------------------------------------------------------------------------
                | Main Heading
                |--------------------------------------------------------------------------
                */

                $sheet->mergeCells('A1:J1');

                $sheet->getStyle('A1:J1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 18,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(30);

                /*
                |--------------------------------------------------------------------------
                | Project Name
                |--------------------------------------------------------------------------
                */

                $sheet->mergeCells('A2:J2');

                $sheet->getStyle('A2:J2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getRowDimension(2)->setRowHeight(24);

                /*
                |--------------------------------------------------------------------------
                | Generated Date / Time
                |--------------------------------------------------------------------------
                */

                $sheet->mergeCells('A3:J3');

                $sheet->getStyle('A3:J3')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 10,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                /*
                |--------------------------------------------------------------------------
                | Applied Filters
                |--------------------------------------------------------------------------
                */

                $sheet->mergeCells('A4:J4');

                $sheet->getStyle('A4:J4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getRowDimension(4)->setRowHeight(30);

                /*
                |--------------------------------------------------------------------------
                | Header Row
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A6:J6')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->getRowDimension(6)->setRowHeight(35);

                /*
                |--------------------------------------------------------------------------
                | Data Rows
                |--------------------------------------------------------------------------
                */

                $highestRow = $sheet->getHighestRow();

                if ($highestRow >= 7) {

                    $sheet->getStyle(
                        "A7:J{$highestRow}"
                    )->applyFromArray([

                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],

                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                    // S.No
                    $sheet->getStyle(
                        "A7:A{$highestRow}"
                    )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );

                    // Size
                    $sheet->getStyle(
                        "F7:F{$highestRow}"
                    )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );

                    // Plot No
                    $sheet->getStyle(
                        "G7:G{$highestRow}"
                    )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );

                    // LOP + Mortgage
                    $sheet->getStyle(
                        "H7:I{$highestRow}"
                    )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Auto Filter
                |--------------------------------------------------------------------------
                */

                $sheet->setAutoFilter(
                    "A6:J{$highestRow}"
                );

                /*
                |--------------------------------------------------------------------------
                | Freeze Header
                |--------------------------------------------------------------------------
                */

                $sheet->freezePane('A7');

                /*
                |--------------------------------------------------------------------------
                | Column Widths
                |--------------------------------------------------------------------------
                */

                $widths = [

                    'A' => 8,
                    'B' => 22,
                    'C' => 18,
                    'D' => 22,
                    'E' => 18,
                    'F' => 16,
                    'G' => 14,
                    'H' => 14,
                    'I' => 14,
                    'J' => 35,

                ];

                foreach ($widths as $column => $width) {

                    $sheet
                        ->getColumnDimension($column)
                        ->setWidth($width);
                }

                /*
                |--------------------------------------------------------------------------
                | Page Setup
                |--------------------------------------------------------------------------
                */

                $sheet->getPageSetup()
                    ->setOrientation(
                        PageSetup::ORIENTATION_LANDSCAPE
                    );

                $sheet->getPageSetup()
                    ->setPaperSize(
                        PageSetup::PAPERSIZE_A4
                    );

                $sheet->getPageSetup()
                    ->setFitToWidth(1);

                $sheet->getPageSetup()
                    ->setFitToHeight(0);

                /*
                |--------------------------------------------------------------------------
                | Page Margins
                |--------------------------------------------------------------------------
                */

                $sheet->getPageMargins()
                    ->setTop(0.5)
                    ->setRight(0.3)
                    ->setLeft(0.3)
                    ->setBottom(0.5);

                /*
                |--------------------------------------------------------------------------
                | Repeat Header Rows When Printing
                |--------------------------------------------------------------------------
                */

                $sheet->getPageSetup()
                    ->setRowsToRepeatAtTopByStartAndEnd(1, 6);
            },
        ];
    }
}
