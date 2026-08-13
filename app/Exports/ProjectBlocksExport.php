<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\Block;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

// class ProjectBlocksExport implements FromCollection

class ProjectBlocksExport implements FromView
{
    protected $projectId;

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    public function view(): View
    {
        $blocks = Block::where('project_id', $this->projectId)
            ->orderBy('block_name')
            ->get();

        return view('exports.project_blocks_excel', [
            'blocks' => $blocks
        ]);
    }
}
