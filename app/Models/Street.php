<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\StreetController;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class Street extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('street')
            // ->logFillable()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) =>
                "Street has been {$eventName}"
            );
    }

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->useLogName('street')
    //         ->logFillable()
    //         ->logOnlyDirty()
    //         ->dontSubmitEmptyLogs()
    //         ->setDescriptionForEvent(fn(string $eventName) =>
    //             "SProject has been {$eventName}"
    //         );
    // }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (auth()->check()) {
            $activity->causer_id = auth()->id();
        }

        $activity->properties = $activity->properties->merge([
            'project_id' => $this->project_id,
            'block_id' => $this->block_id,
            
        ]);

    }

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function plots()
    {
        return $this->hasMany(Plot::class);
    }

}
