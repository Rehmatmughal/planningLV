<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;


class DevelopmentStatus extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Development_status')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => 
                "Development Status has been {$eventName}"
            );
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (auth()->check()) {
            $activity->causer_id = auth()->id();
        }

        $activity->properties = $activity->properties->merge([
            'plot_id' => $this->plot_id,
        ]);
    }

    protected $fillable = [
        'plot_id', 'sewer_manholes', 'asphalt_tst', 'overall_status', 'remarks'
    ];

    public function plot() {
        return $this->belongsTo(Plot::class);
    }
}

