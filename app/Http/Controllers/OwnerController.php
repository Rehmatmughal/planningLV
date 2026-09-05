<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OwnerController extends Controller
{
    /**
     * Display all owners.
     */
    public function index(Request $request)
    {
        $query = Owner::query();

        // Search by name, CNIC or contact
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('owner_name', 'like', "%{$search}%")
                    ->orWhere('cnic', 'like', "%{$search}%")
                    ->orWhere('contact_no', 'like', "%{$search}%");

            });
        }

        $owners = $query
            ->orderBy('owner_name')
            ->paginate(25)
            ->withQueryString();

        return view('owners.index', compact('owners'));
    }


    /**
     * Show create form.
     */
    public function create()
    {
        return view('owners.create');
    }


    /**
     * Store new owner.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'owner_name' => [
                'required',
                'string',
                'max:255',
            ],

            'cnic' => [
                'required',
                'string',
                'max:30',
                'unique:owners,cnic',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'contact_no' => [
                'nullable',
                'string',
                'max:50',
            ],

        ]);


        Owner::create($validated);


        return redirect()
            ->route('owners.index')
            ->with('success', 'Owner added successfully.');
    }


    /**
     * Show edit form.
     */
    public function edit(Owner $owner)
    {
        return view('owners.edit', compact('owner'));
    }


    /**
     * Update owner.
     */
    public function update(Request $request, Owner $owner)
    {
        $validated = $request->validate([

            'owner_name' => [
                'required',
                'string',
                'max:255',
            ],

            'cnic' => [
                'required',
                'string',
                'max:30',
                Rule::unique('owners', 'cnic')
                    ->ignore($owner->id),
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'contact_no' => [
                'nullable',
                'string',
                'max:50',
            ],

        ]);


        $owner->update($validated);


        return redirect()
            ->route('owners.index')
            ->with('success', 'Owner updated successfully.');
    }


    /**
     * Delete owner.
     */
    public function destroy(Owner $owner)
    {
        // Do not delete an owner if linked with possession cases
        if ($owner->possessionCases()->exists()) {

            return redirect()
                ->route('owners.index')
                ->with('error',
                    'This owner cannot be deleted because they are linked with possession cases.'
                );
        }


        $owner->delete();


        return redirect()
            ->route('owners.index')
            ->with('success', 'Owner deleted successfully.');
    }
}
