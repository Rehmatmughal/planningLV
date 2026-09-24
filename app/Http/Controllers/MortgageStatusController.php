<?php

namespace App\Http\Controllers;

use App\Models\MortgageStatus;
use App\Models\LopStatus;
use Illuminate\Http\Request;

class MortgageStatusController extends Controller
{
    /**
     * Check whether mortgage YES is allowed for a plot.
     *
     * If $lopStatus is supplied, that value is checked.
     * Otherwise current database LOP status is checked.
     */
    public function isMortgageAllowed($plotId, $lopStatus = null): bool
    {
        if ($lopStatus === null) {
            $lopStatus = LopStatus::where('plot_id', $plotId)
                ->value('lop_status');
        }

        return $lopStatus === 'lop';
    }

    /**
     * Save / Update Mortgage Status
     */
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'plot_id' => 'required|exists:plots,id',
            'is_mortgaged' => 'required|in:yes,no',
            'remarks' => 'nullable|string',
        ]);

        /*
         * Mortgage YES is only allowed when
         * LOP status is "lop".
         */
        if ($request->is_mortgaged === 'yes') {

            if (!$this->isMortgageAllowed($request->plot_id)) {
                return back()
                    ->withErrors([
                        'is_mortgaged' =>
                            'Mortgage YES sirf un plots ke liye allowed hai jinka LOP status "lop" ho.'
                    ])
                    ->withInput();
            }
        }

        MortgageStatus::updateOrCreate(
            ['plot_id' => $request->plot_id],
            [
                'is_mortgaged' => $request->is_mortgaged,
                'remarks' => $request->remarks,
            ]
        );

        return back()->with('success', 'Mortgage status saved!');
    }
}
