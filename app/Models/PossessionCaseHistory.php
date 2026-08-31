<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PossessionCaseHistory extends Model
{
    use HasFactory;

    protected $table = 'possession_case_histories';

    protected $fillable = [
        'possession_case_id',
        'plot_id',
        'action',
        'old_status',
        'new_status',
        'old_holder',
        'new_holder',
        'handed_over_to',
        'remarks',
        'user_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // History belongs to a possession case
    public function possessionCase()
    {
        return $this->belongsTo(PossessionCase::class);
    }

    // History belongs to a plot
    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    // User who performed this action
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
