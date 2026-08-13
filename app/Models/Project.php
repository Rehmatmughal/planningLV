<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;


class Project extends Model
{
    use HasFactory, LogsActivity;
    // use SoftDeletes;

    // use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('project')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) =>
                "Project has been {$eventName}"
            );
    }

    // public $timestamps = false;
    protected $fillable = [
        'project_name',
        'project_remarks',
    ];

    public function plots() {
        return $this->hasMany(Plot::class);
    }

    public function sizes() {
        return $this->hasMany(Size::class);
    }
    public function blocks() {
        return $this->hasMany(Block::class);
    }

    public function streets() {
        return $this->hasMany(Street::class);
    }
    
}
