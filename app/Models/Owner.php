<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_name',
        'cnic',
        'address',
        'contact_no',
    ];


    /*
    |--------------------------------------------------------------------------
    | Possession Cases
    |--------------------------------------------------------------------------
    */

    public function possessionCases()
    {
        return $this->belongsToMany(
            PossessionCase::class,
            'possession_case_owners'
        )
        ->withPivot('ownership_percentage')
         ->withTimestamps();
    }
}
