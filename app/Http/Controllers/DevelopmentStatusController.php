<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentStatus;
use Illuminate\Http\Request;

class DevelopmentStatusController extends Controller
{
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'plot_id' => 'required',
            'sewer_manholes' => 'required',
            'asphalt_tst' => 'required',
            'overall_status' => 'required',
            'remarks' => 'nullable'
        ]);

        DevelopmentStatus::updateOrCreate(
            ['plot_id' => $request->plot_id],
            [
                'sewer_manholes' => $request->sewer_manholes,
                'asphalt_tst' => $request->asphalt_tst,
                'overall_status' => $request->overall_status,
                'remarks' => $request->remarks
            ]
        );

        return back()->with('success', 'Development status saved!');
    }
}
