<?php

namespace App\Exports;

use App\Models\Plot;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DevelopmentExport implements FromView
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

        return view('exports.development_excel', compact('plots'));
    }
}
