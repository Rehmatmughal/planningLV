<?php

namespace App\Exports;

use App\Models\Plot;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BlockPlotsExport implements FromView
{
    protected $block;
    protected $request;

    public function __construct($block, $request)
    {
        $this->block   = $block;
        $this->request = $request;
    }

    public function view(): View
    {
        $plots = Plot::with(['street', 'plotsize'])
            ->where('block_id', $this->block->id)
            ->when($this->request->street_id, fn($q) =>
                $q->where('street_id', $this->request->street_id)
            )
            ->when($this->request->plot_number, fn($q) =>
                $q->where('plot_number', 'like', '%' . $this->request->plot_number . '%')
            )
            ->orderBy('plot_number')
            ->get(); // ⚠ no pagination for Excel

        return view('exports.block_plots_excel', [
            'block' => $this->block,
            'plots' => $plots,
        ]);
    }
}

