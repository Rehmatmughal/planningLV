<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyTypeAssignment extends Model
{
    protected $table = 'property_type_assignments';

    protected $fillable = [
        'project_id',
        'block_id',
        'property_type_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }
}