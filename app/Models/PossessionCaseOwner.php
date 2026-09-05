<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PossessionCaseOwner extends Model
{
    use HasFactory;

    protected $fillable = [
        'possession_case_id',
        'owner_id',
        'ownership_percentage',
    ];


    /*
    |--------------------------------------------------------------------------
    | Possession Case
    |--------------------------------------------------------------------------
    */

    public function possessionCase()
    {
        return $this->belongsTo(
            PossessionCase::class,
            'possession_case_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Owner
    |--------------------------------------------------------------------------
    */

    public function owner()
    {
        return $this->belongsTo(
            Owner::class,
            'owner_id'
        );
    }
}