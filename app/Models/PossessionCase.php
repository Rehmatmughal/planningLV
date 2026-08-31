<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PossessionCase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'possession_cases';

    protected $fillable = [
        'plot_id',
        'case_no',
        'need_approval',
        'current_status',
        'current_holder_type',
        'current_holder_id',
        'current_holder_name',
        'received_at',
        'prepared_at',
        'signed_at',
        'approval_sent_at',
        'received_back_at',
        'handed_over_at',
        'completed_at',
        'handed_over_to',
        'remarks',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'need_approval' => 'boolean',
        'is_active' => 'boolean',

        'received_at' => 'date',
        'prepared_at' => 'date',
        'signed_at' => 'date',
        'approval_sent_at' => 'date',
        'received_back_at' => 'date',
        'handed_over_at' => 'date',
        'completed_at' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Possession Case belongs to a Plot
    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    // A possession case can have multiple owners
    public function owners()
    {
        return $this->hasMany(PossessionCaseOwner::class);
    }

    // A possession case has multiple history records
    public function histories()
    {
        return $this->hasMany(PossessionCaseHistory::class);
    }

    // User who created the case
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // User who last updated the case
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
