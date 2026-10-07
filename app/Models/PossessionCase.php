<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PossessionCaseHistory;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

// use Spatie\Activitylog\Traits\LogsActivity;
// use Illuminate\Database\Eloquent\SoftDeletes;
// use Spatie\Activitylog\LogOptions;
// use Spatie\Activitylog\Models\Activity;



class PossessionCase extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'possession_cases';

    protected $fillable = [
        'plot_id',
        'possession_no',
        'reference_no',
        'possession_sequence',
        'revision_no',
        'need_approval',
        'current_status',
        'current_holder_type',
        'current_holder_id',
        'current_holder_name',
        'received_at',
        'prepared_at',
        'surveyor_signed_at',
        'signed_at',
        'town_planner_signed_at',
        'approval_sent_at',
        'received_back_at',
        'handed_over_at',
        'completed_at',
        'handed_over_to',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
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
        'surveyor_signed_at' => 'date',
        'signed_at' => 'date',
        'town_planner_signed_at' => 'date',
        'approval_sent_at' => 'date',
        'received_back_at' => 'date',
        'handed_over_at' => 'date',
        'completed_at' => 'date',
        'cancelled_at' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function owners()
    {
        return $this->belongsToMany(
            Owner::class,
            'possession_case_owners'
        )
        ->withPivot(
            'address_snapshot'
        )
        ->withTimestamps();
    }

    public function histories()
    {
        return $this->hasMany(
            PossessionCaseHistory::class,
            'possession_case_id'
        )->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('PossessionCase')
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $eventName) =>
                    "Possession {$this->possession_no} has been {$eventName}"
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Add readable possession information to Activity Log
    |--------------------------------------------------------------------------
    */

    public function tapActivity(
        Activity $activity,
        string $eventName
    ) {
        /*
        |--------------------------------------------------------------------------
        | Logged-in user
        |--------------------------------------------------------------------------
        */

        if (auth()->check()) {
            $activity->causer_id = auth()->id();
        }

        /*
        |--------------------------------------------------------------------------
        | Load required relationships
        |--------------------------------------------------------------------------
        */

        $this->loadMissing([
            'plot.project',
            'plot.block',
            'plot.street',
            'plot.size',
            'plot.propertyType',
            'owners',
        ]);

        $plot = $this->plot;

        /*
        |--------------------------------------------------------------------------
        | Possession Context
        |--------------------------------------------------------------------------
        */

        $activity->properties = $activity->properties->merge([
            'possession_context' => [
                'possession_no' => $this->possession_no,
                'reference_no' => $this->reference_no,

                'project_id' => $plot?->project_id,
                'project_name' => $plot?->project?->project_name,

                'block_id' => $plot?->block_id,
                'block_name' => $plot?->block?->block_name,

                'street_id' => $plot?->street_id,
                'street_name' => $plot?->street?->street_name,

                'plot_id' => $plot?->id,
                'plot_number' => $plot?->plot_number,

                'property_type_id' => $plot?->property_type_id,
                'property_type_name' => $plot?->propertyType?->name,

                'size_id' => $plot?->size_id,
                'size_name' => $plot?->size?->name,

                'owners' => $this->owners
                    ->map(function ($owner) {
                        return [
                            'id' => $owner->id,
                            'name' => $owner->owner_name,
                            'relative_name' => $owner->relative_name,
                            'cnic' => $owner->cnic,
                        ];
                    })
                    ->values()
                    ->all(),
            ],
        ]);
    }
}