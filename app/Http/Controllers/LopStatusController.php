<?php

namespace App\Http\Controllers;

use App\Models\LopStatus;
use Illuminate\Http\Request;

class LopStatusController extends Controller
{
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'plot_id' => 'required|exists:plots,id',
            'lop_status' => 'required|in:lop,non_lop',
            'remarks' => 'nullable'
        ]);

        LopStatus::updateOrCreate(
            ['plot_id' => $request->plot_id],
            [
                'lop_status' => $request->lop_status,
                'remarks' => $request->remarks
            ]
        );

        return back()->with('success', 'LOP status saved!');
    }
}
