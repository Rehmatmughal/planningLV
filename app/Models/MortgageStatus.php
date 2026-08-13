<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;


class MortgageStatus extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['plot_id', 'is_mortgaged', 'remarks'];

    // protected static function booted()
    // {
    //     static::saving(function ($mortgage) {

    //         if ($mortgage->is_mortgaged === 'yes') {

    //             $lop = $mortgage->plot->lopStatus;

    //             if (!$lop || $lop->lop_status !== 'lop') {
    //                 throw new \Exception("Mortgage YES is only allowed for LOP plots.");
    //             }
    //         }
    //     });
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Mortgage_status')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => 
                "Mortgage Status has been {$eventName}"
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


    public function plot() {
        return $this->belongsTo(Plot::class);
    }
    
}
