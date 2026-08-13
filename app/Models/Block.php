<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;


class Block extends Model
{
    use LogsActivity;
    // use SoftDeletes;
    
    protected $fillable = ['project_id', 'block_name', 'remarks'];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('block')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) =>
                "Block has been {$eventName}"
            );
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (auth()->check()) {
            $activity->causer_id = auth()->id();
        }

        $activity->properties = $activity->properties->merge([
            'project_id' => $this->project_id,
        ]);
    }


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function streets()
    {
        return $this->hasMany(Street::class);
    }

    public function plots()
    {
        return $this->hasMany(Plot::class);
    }

}
