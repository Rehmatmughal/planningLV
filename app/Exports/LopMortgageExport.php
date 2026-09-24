<?php

namespace App\Exports;

use App\Models\Plot;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LopMortgageExport implements FromView
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

        return view('exports.lop_mortgage_excel', [
            'plots' => $plots,
        ]);
    }
}
