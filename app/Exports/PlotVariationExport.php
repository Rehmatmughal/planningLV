<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PlotVariationExport implements FromView
{
    protected $plot;
    protected $variation;

    public function __construct($plot,$variation)
    {
        $this->plot = $plot;
        $this->variation = $variation;
    }

    public function view(): View
    {
        return view('exports.plot_variation_report',[
            'plot' => $this->plot,
            'av'   => $this->variation
        ]);
    }
}