<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PlotSizeAssignment;
use App\Models\PropertyTypeAssignment;


class PropertyType extends Model
{
    protected $fillable = [
        'name',
    ];

    public function plotSizeAssignments()
    {
        return $this->hasMany(PlotSizeAssignment::class);
    }

    public function propertyTypeAssignments()
    {
        return $this->hasMany(
            PropertyTypeAssignment::class,
            'property_type_id'
        );
    }

}