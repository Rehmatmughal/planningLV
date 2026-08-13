<?php

namespace App\Http\Controllers;

use App\Models\MortgageStatus;
use App\Models\LopStatus;
use Illuminate\Http\Request;

class MortgageStatusController extends Controller
{
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'plot_id' => 'required|exists:plots,id',
            'is_mortgaged' => 'required|in:yes,no',
            'remarks' => 'nullable'
        ]);
                // 🔎 LOP Status Check
        if ($request->is_mortgaged === 'yes') {

            $lop = LopStatus::where('plot_id', $request->plot_id)->first();

            if (!$lop || $lop->lop_status !== 'lop') {
                return back()
                    ->withErrors([
                        'is_mortgaged' => 'Mortgage YES sirf un plots ke liye allowed hai jinka LOP status "lop" ho.'
                    ])
                    ->withInput();
            }
        }

        MortgageStatus::updateOrCreate(
            ['plot_id' => $request->plot_id],
            [
                'is_mortgaged' => $request->is_mortgaged,
                'remarks' => $request->remarks
            ]
        );

        return back()->with('success', 'Mortgage status saved!');
    
        // MortgageStatus::updateOrCreate(
        //     ['plot_id' => $request->plot_id],
        //     [
        //         'is_mortgaged' => $request->is_mortgaged,
        //         'remarks' => $request->remarks
        //     ]
        // );

        // return back()->with('success', 'Mortgage status saved!');
    }
}
