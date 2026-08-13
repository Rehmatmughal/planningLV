<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaVariation extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $guarded = [];
    // protected $fillable = [
    //     'plot_id',
    //     'measured_area',
    //     'measured_by',
    //     'measured_date',
    //     'remarks',
    //     'source',
    //     // snapshot fields
    //     'road_status_at_time',
    //     'sewer_status_at_time',
    //     'lop_status_at_time',
    // ];

    protected $casts = [
        'measured_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Activity Log Configuration
    |--------------------------------------------------------------------------
    */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('area_variation')
            // ->logFillable()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => 
                "Area Variation has been {$eventName}"
            );
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (auth()->check()) {
            $activity->causer_id = auth()->id();
        }

        // Extra useful info store karna
        $activity->properties = $activity->properties->merge([
            'plot_id' => $this->plot_id,
            'project_id' => optional($this->plot)->project_id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }
}
