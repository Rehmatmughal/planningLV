<?php

namespace App\Http\Controllers;

use App\Models\PossessionStatus;
use Illuminate\Http\Request;

class PossessionStatusController extends Controller
{
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'plot_id' => 'required',
            'possession_status' => 'required',
            'remarks' => 'nullable'
        ]);

        PossessionStatus::updateOrCreate(
            ['plot_id' => $request->plot_id],
            [
                'possession_status' => $request->possession_status,
                'remarks' => $request->remarks
            ]
        );

        return back()->with('success', 'Possession status saved!');
    }
}
