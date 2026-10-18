<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PossessionCaseReportExport implements WithMultipleSheets
{
    protected array $detailedSummaries;
    protected array $overallSummaries;
    protected string $reportDate;

    public function __construct(
        array $detailedSummaries,
        array $overallSummaries,
        string $reportDate
    ) {
        $this->detailedSummaries = $detailedSummaries;
        $this->overallSummaries = $overallSummaries;
        $this->reportDate = $reportDate;
    }

    public function sheets(): array
    {
        return [
            new PossessionCaseDetailSheet(
                'Residential Possessions',
                $this->detailedSummaries['residential_possessions'] ?? [],
                $this->reportDate
            ),
            new PossessionCaseDetailSheet(
                'Residential Re-Possessions',
                $this->detailedSummaries['residential_repossessions'] ?? [],
                $this->reportDate
            ),
            new PossessionCaseDetailSheet(
                'Commercial Possessions',
                $this->detailedSummaries['commercial_possessions'] ?? [],
                $this->reportDate
            ),
            new PossessionCaseDetailSheet(
                'Commercial Re-Possessions',
                $this->detailedSummaries['commercial_repossessions'] ?? [],
                $this->reportDate
            ),
            new PossessionCaseOverallSheet(
                'Residential Overall',
                $this->overallSummaries['residential'] ?? [],
                $this->reportDate
            ),
            new PossessionCaseOverallSheet(
                'Commercial Overall',
                $this->overallSummaries['commercial'] ?? [],
                $this->reportDate
            ),
        ];
    }
}

class PossessionCaseDetailSheet implements FromArray, WithTitle, ShouldAutoSize, WithStyles
{
    protected string $sheetTitle;
    protected array $summaries;
    protected string $reportDate;

    public function __construct(
        string $sheetTitle,
        array $summaries,
        string $reportDate
    ) {
        $this->sheetTitle = $sheetTitle;
        $this->summaries = $summaries;
        $this->reportDate = $reportDate;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function array(): array
    {
        $rows = [
            ['Possession Cases Report'],
            ['Report Date', $this->reportDate],
            [],
        ];

        if (empty($this->summaries)) {
            $rows[] = ['No records found for the selected filters.'];

            return $rows;
        }

        foreach ($this->summaries as $summary) {
            $sizes = $summary['sizes'] ?? [];

            $rows[] = ['Project', $summary['project_name'] ?? ''];
            $rows[] = ['Property Type', $summary['property_type_name'] ?? ''];
            $rows[] = array_merge(['Block'], $sizes, ['Total']);

            $sizeTotals = array_fill_keys($sizes, 0);

            foreach (($summary['blocks'] ?? []) as $block) {
                $counts = [];

                foreach ($sizes as $size) {
                    $count = (int) ($block['counts'][$size] ?? 0);
                    $counts[] = $count;
                    $sizeTotals[$size] += $count;
                }

                $rows[] = array_merge(
                    [$block['block_name'] ?? ''],
                    $counts,
                    [(int) ($block['total'] ?? 0)]
                );
            }

            $rows[] = array_merge(
                ['Grand Total'],
                array_values($sizeTotals),
                [(int) ($summary['grand_total'] ?? 0)]
            );

            $rows[] = [];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
            ],
        ];
    }
}

class PossessionCaseOverallSheet implements FromArray, WithTitle, ShouldAutoSize, WithStyles
{
    protected string $sheetTitle;
    protected array $summaries;
    protected string $reportDate;

    public function __construct(
        string $sheetTitle,
        array $summaries,
        string $reportDate
    ) {
        $this->sheetTitle = $sheetTitle;
        $this->summaries = $summaries;
        $this->reportDate = $reportDate;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function array(): array
    {
        $rows = [
            ['Possession Cases Report'],
            ['Report Date', $this->reportDate],
            [],
        ];

        if (empty($this->summaries)) {
            $rows[] = ['No records found for the selected filters.'];

            return $rows;
        }

        $projectGroups = collect($this->summaries)->groupBy('project_name');

        foreach ($projectGroups as $projectName => $projectRows) {
            $rows[] = ['Project', $projectName];
            $rows[] = [
                'Sr. No.',
                'Block',
                'Possession',
                'Re-possession',
                'Total',
            ];

            $sr = 1;
            $possessionTotal = 0;
            $rePossessionTotal = 0;
            $grandTotal = 0;

            foreach ($projectRows as $item) {
                $possession = (int) ($item['possession'] ?? 0);
                $rePossession = (int) ($item['re_possession'] ?? 0);
                $total = (int) ($item['total'] ?? 0);

                $rows[] = [
                    $sr++,
                    $item['block_name'] ?? '',
                    $possession,
                    $rePossession,
                    $total,
                ];

                $possessionTotal += $possession;
                $rePossessionTotal += $rePossession;
                $grandTotal += $total;
            }

            $rows[] = [
                'Project Total',
                '',
                $possessionTotal,
                $rePossessionTotal,
                $grandTotal,
            ];

            $rows[] = [];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
            ],
        ];
    }
}
