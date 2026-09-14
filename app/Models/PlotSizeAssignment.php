<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlotSizeAssignment extends Model
{
    protected $table = 'plot_size_assignments';

    protected $fillable = [
        'project_id',
        'property_type_id',
        'block_id',
        'plotsize_id',
    ];

    /**
     * Assignment belongs to a Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Assignment belongs to a Property Type
     */
    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    /**
     * Assignment belongs to a Block
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Assignment belongs to a Plot Size
     */
    public function plotsize()
    {
        return $this->belongsTo(Plotsize::class, 'plotsize_id');
    }
}