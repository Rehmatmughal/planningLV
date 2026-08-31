<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PossessionCaseOwner extends Model
{
    use HasFactory;

    protected $table = 'possession_case_owners';

    protected $fillable = [
        'possession_case_id',
        'owner_name',
        'cnic',
        'address',
        'contact_no',
        'ownership_percentage',
    ];

    protected $casts = [
        'ownership_percentage' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Owner belongs to a possession case
    public function possessionCase()
    {
        return $this->belongsTo(PossessionCase::class);
    }
}
