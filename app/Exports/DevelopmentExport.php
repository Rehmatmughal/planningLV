<?php

namespace App\Exports;

use App\Models\Plot;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class DevelopmentExport implements FromView, WithEvents
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $plots = Plot::with([
            'project',
            'block',
            'street',
            'propertyType',
            'size',
            'developmentStatus',
        ])
        ->when($this->filters['project_id'] ?? null, function ($query, $projectId) {
            $query->where('project_id', $projectId);
        })
        ->when($this->filters['block_id'] ?? null, function ($query, $blockId) {
            $query->where('block_id', $blockId);
        })
        ->when($this->filters['street_id'] ?? null, function ($query, $streetId) {
            $query->where('street_id', $streetId);
        })
        ->when($this->filters['property_type_id'] ?? null, function ($query, $propertyTypeId) {
            $query->where('property_type_id', $propertyTypeId);
        })
        ->when($this->filters['overall_status'] ?? null, function ($query, $status) {
            $query->whereHas('developmentStatus', function ($q) use ($status) {
                $q->where('overall_status', $status);
            });
        })
        ->when($this->filters['sewer_manholes'] ?? null, function ($query, $status) {
            $query->whereHas('developmentStatus', function ($q) use ($status) {
                $q->where('sewer_manholes', $status);
            });
        })
        ->when($this->filters['asphalt_tst'] ?? null, function ($query, $status) {
            $query->whereHas('developmentStatus', function ($q) use ($status) {
                $q->where('asphalt_tst', $status);
            });
        })
        ->when($this->filters['plot_number'] ?? null, function ($query, $plotNumber) {
            $query->where('plot_number', 'like', '%' . $plotNumber . '%');
        })
        ->orderBy('plot_number')
        ->get();

        /*
        |--------------------------------------------------------------------------
        | Project Names
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

        if (!empty($this->filters['project_id'])) {
            $project = \App\Models\Project::find($this->filters['project_id']);

            if ($project) {
                $appliedFilters[] = 'Project: ' . $project->project_name;
            }
        }

        if (!empty($this->filters['block_id'])) {
            $block = \App\Models\Block::find($this->filters['block_id']);

            if ($block) {
                $appliedFilters[] = 'Block: ' . $block->block_name;
            }
        }

        if (!empty($this->filters['street_id'])) {
            $street = \App\Models\Street::find($this->filters['street_id']);

            if ($street) {
                $appliedFilters[] = 'Street: ' . $street->street_name;
            }
        }

        if (!empty($this->filters['property_type_id'])) {
            $propertyType = \App\Models\PropertyType::find(
                $this->filters['property_type_id']
            );

            if ($propertyType) {
                $appliedFilters[] = 'Property Type: ' . $propertyType->name;
            }
        }

        if (!empty($this->filters['overall_status'])) {
            $appliedFilters[] = 'Overall Status: ' .
                ucwords(str_replace('_', ' ', $this->filters['overall_status']));
        }

        if (!empty($this->filters['sewer_manholes'])) {
            $appliedFilters[] = 'Sewerage / Manholes: ' .
                ucwords(str_replace('_', ' ', $this->filters['sewer_manholes']));
        }

        if (!empty($this->filters['asphalt_tst'])) {
            $appliedFilters[] = 'Asphalt / TST: ' .
                strtoupper($this->filters['asphalt_tst']);
        }

        if (!empty($this->filters['plot_number'])) {
            $appliedFilters[] = 'Plot No: ' . $this->filters['plot_number'];
        }

        return view('exports.development_excel', [
            'plots' => $plots,
            'projectTitle' => $projectTitle,
            'appliedFilters' => $appliedFilters,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                /*
                |--------------------------------------------------------------------------
                | Main Report Title
                |--------------------------------------------------------------------------
                */

                $sheet->mergeCells('A1:K1');

                $sheet->getStyle('A1:K1')->applyFromArray([
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

                $sheet->mergeCells('A2:K2');

                $sheet->getStyle('A2:K2')->applyFromArray([
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
                | Generated Date
                |--------------------------------------------------------------------------
                */

                $sheet->mergeCells('A3:K3');

                $sheet->getStyle('A3:K3')->applyFromArray([
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

                $sheet->mergeCells('A4:K4');

                $sheet->getStyle('A4:K4')->applyFromArray([
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
                | Table Header
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A6:K6')->applyFromArray([
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
                | Data Area
                |--------------------------------------------------------------------------
                */

                $highestRow = $sheet->getHighestRow();

                if ($highestRow >= 7) {
                    $sheet->getStyle("A7:K{$highestRow}")->applyFromArray([
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

                    $sheet->getStyle("A7:A{$highestRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("G7:G{$highestRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("H7:J{$highestRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                /*
                |--------------------------------------------------------------------------
                | Auto Filter
                |--------------------------------------------------------------------------
                */

                $sheet->setAutoFilter("A6:K{$highestRow}");

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
                    'H' => 22,
                    'I' => 16,
                    'J' => 20,
                    'K' => 35,
                ];

                foreach ($widths as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }

                /*
                |--------------------------------------------------------------------------
                | Print Settings
                |--------------------------------------------------------------------------
                */

                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

                $sheet->getPageSetup()
                    ->setPaperSize(PageSetup::PAPERSIZE_A4);

                $sheet->getPageSetup()
                    ->setFitToWidth(1);

                $sheet->getPageSetup()
                    ->setFitToHeight(0);

                $sheet->getPageMargins()
                    ->setTop(0.5)
                    ->setRight(0.3)
                    ->setLeft(0.3)
                    ->setBottom(0.5);

                /*
                |--------------------------------------------------------------------------
                | Repeat Header Row on Printed Pages
                |--------------------------------------------------------------------------
                */

                $sheet->getPageSetup()
                    ->setRowsToRepeatAtTopByStartAndEnd(1, 6);
            },
        ];
    }
}
