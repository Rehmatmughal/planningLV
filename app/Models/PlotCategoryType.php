<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;


class PlotCategoryType extends Model
{
    protected $guarded =[]; 
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Plot_Category')
            // ->logFillable()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) =>
                "Plot Category has been {$eventName}"
            );
    }

    public function plot()
    {        
        return $this->hasOne(Plot::class);

    }
    // public function project()
    // {        
    //     return $this->belongsTo(Project::class);

    // }
}
