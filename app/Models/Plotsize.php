<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;


class Plotsize extends Model
{
    use SoftDeletes, LogsActivity;
    // use HasFactory, LogsActivity;


    protected $table = 'plotsizes';

    protected $fillable = [
        'title',
        'size_area',
        'project_id',
        'remarks',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('plot_size')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => 
                "Plot Size has been {$eventName}"
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
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function plot()
    {
        return $this->hasMany(Plot::class, 'size_id');
    }

    // public function streets()
    // {
    //     return $this->hasMany(Street::class);
    // }

}
