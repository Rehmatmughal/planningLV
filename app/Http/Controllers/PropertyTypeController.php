<?php

namespace App\Http\Controllers;

use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PropertyTypeController extends Controller
{
    /**
     * Display a listing of property types.
     */
    public function index()
    {
        $propertyTypes = PropertyType::orderBy('name')->paginate(10);

        return view('property-types.index', compact('propertyTypes'));
    }

    /**
     * Show the form for creating a new property type.
     */
    public function create()
    {
        return view('property-types.create');
    }

    /**
     * Store a newly created property type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:property_types,name',
            ],
        ]);

        PropertyType::create($validated);

        return redirect()
            ->route('property-types.index')
            ->with('success', 'Property Type created successfully.');
    }

    /**
     * Show the form for editing the specified property type.
     */
    public function edit(PropertyType $propertyType)
    {
        return view('property-types.edit', compact('propertyType'));
    }

    /**
     * Update the specified property type.
     */
    public function update(Request $request, PropertyType $propertyType)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('property_types', 'name')
                    ->ignore($propertyType->id),
            ],
        ]);

        $propertyType->update($validated);

        return redirect()
            ->route('property-types.index')
            ->with('success', 'Property Type updated successfully.');
    }

    /**
     * Remove the specified property type.
     */
    public function destroy(PropertyType $propertyType)
    {
        $propertyType->delete();

        return redirect()
            ->route('property-types.index')
            ->with('success', 'Property Type deleted successfully.');
    }
}
