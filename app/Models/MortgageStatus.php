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

    protected $fillable = [
        'plot_id',
        'is_mortgaged',
        'remarks',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Mortgage_status')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn(string $eventName) =>
                    "Mortgage Status has been {$eventName}"
            );
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (auth()->check()) {
            $activity->causer_id = auth()->id();
        }

        $this->loadMissing([
            'plot.project',
            'plot.block',
            'plot.street',
            'plot.size',
            'plot.propertyType',
        ]);

        $plot = $this->plot;

        $activity->properties = $activity->properties->merge([
            'plot_context' => [
                'plot_id' => $plot?->id,

                'project_id' => $plot?->project_id,
                'project_name' => $plot?->project?->project_name,

                'block_id' => $plot?->block_id,
                'block_name' => $plot?->block?->block_name,

                'street_id' => $plot?->street_id,
                'street_name' => $plot?->street?->street_name,

                'plot_number' => $plot?->plot_number,

                'property_type_id' => $plot?->property_type_id,
                'property_type_name' => $plot?->propertyType?->name,

                'size_id' => $plot?->size_id,
                'size_title' => $plot?->size?->size_title,
            ],
        ]);
    }

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }
}


// <?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Spatie\Activitylog\Traits\LogsActivity;
// use Spatie\Activitylog\LogOptions;
// use Spatie\Activitylog\Models\Activity;


// class MortgageStatus extends Model
// {
//     use HasFactory, LogsActivity;

//     protected $fillable = ['plot_id', 'is_mortgaged', 'remarks'];

//     // protected static function booted()
//     // {
//     //     static::saving(function ($mortgage) {

//     //         if ($mortgage->is_mortgaged === 'yes') {

//     //             $lop = $mortgage->plot->lopStatus;

//     //             if (!$lop || $lop->lop_status !== 'lop') {
//     //                 throw new \Exception("Mortgage YES is only allowed for LOP plots.");
//     //             }
//     //         }
//     //     });
//     // }

//     public function getActivitylogOptions(): LogOptions
//     {
//         return LogOptions::defaults()
//             ->useLogName('Mortgage_status')
//             ->logFillable()
//             ->logOnlyDirty()
//             ->dontSubmitEmptyLogs()
//             ->setDescriptionForEvent(fn(string $eventName) => 
//                 "Mortgage Status has been {$eventName}"
//             );
//     }

//     public function tapActivity(Activity $activity, string $eventName)
//     {
//         if (auth()->check()) {
//             $activity->causer_id = auth()->id();
//         }

//         $activity->properties = $activity->properties->merge([
//             'plot_id' => $this->plot_id,
//         ]);
//     }


//     public function plot() {
//         return $this->belongsTo(Plot::class);
//     }
    
// }
