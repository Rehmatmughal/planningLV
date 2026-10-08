<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class AreaVariation extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'plot_id',
        'previous_area',
        'measured_area',
        'measured_by',
        'measured_date',
        'remarks',
        'source',

        // Snapshot fields
        'road_status_at_time',
        'sewer_status_at_time',
        'lop_status_at_time',
        'overall_status_at_time',
        'mortgage_status_at_time',
        'possession_status',
        'workflow_status',
    ];

    protected $casts = [
        'measured_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('area_variation')
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn(string $eventName) =>
                    "Area Variation has been {$eventName}"
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

        $measuredByName = null;

        if ($this->measured_by) {
            $measuredByName = User::find($this->measured_by)?->name;
        }

        $activity->properties = $activity->properties->merge([
            'area_variation_context' => [
                'area_variation_id' => $this->id,

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

                'measured_by' => $this->measured_by,
                'measured_by_name' => $measuredByName,
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
// use Illuminate\Database\Eloquent\SoftDeletes;

// use Spatie\Activitylog\Traits\LogsActivity;
// use Spatie\Activitylog\LogOptions;
// use Spatie\Activitylog\Models\Activity;

// class AreaVariation extends Model
// {
//     use HasFactory, LogsActivity, SoftDeletes;


//     protected $fillable = [
//         'plot_id',
//         'previous_area',
//         'measured_area',
//         'measured_by',
//         'measured_date',
//         'remarks',
//         'source',

//         // Snapshot fields
//         'road_status_at_time',
//         'sewer_status_at_time',
//         'lop_status_at_time',
//         'overall_status_at_time',
//         'mortgage_status_at_time',
//         'possession_status',
//         'workflow_status',
//     ];


//     protected $casts = [
//         'measured_date' => 'date',
//     ];


//     /*
//     |--------------------------------------------------------------------------
//     | Activity Log Configuration
//     |--------------------------------------------------------------------------
//     */

//     public function getActivitylogOptions(): LogOptions
//     {
//         return LogOptions::defaults()
//             ->useLogName('area_variation')
//             ->logAll()
//             ->logOnlyDirty()
//             ->dontSubmitEmptyLogs()
//             ->setDescriptionForEvent(
//                 fn (string $eventName) =>
//                     "Area Variation has been {$eventName}"
//             );
//     }


//     public function tapActivity(Activity $activity, string $eventName)
//     {
//         if (auth()->check()) {
//             $activity->causer_id = auth()->id();
//         }


//         /*
//         |--------------------------------------------------------------------------
//         | Load Complete Plot Context
//         |--------------------------------------------------------------------------
//         */

//         $this->loadMissing([
//             'plot.project',
//             'plot.block',
//             'plot.street',
//             'plot.size',
//             'plot.propertyType',
//         ]);


//         $plot = $this->plot;


//         /*
//         |--------------------------------------------------------------------------
//         | Measured By User
//         |--------------------------------------------------------------------------
//         */

//         $measuredByName = null;

//         if ($this->measured_by) {
//             $measuredByName = User::find($this->measured_by)?->name;
//         }


//         /*
//         |--------------------------------------------------------------------------
//         | Store Readable Area Variation Context
//         |--------------------------------------------------------------------------
//         */

//         $activity->properties = $activity->properties->merge([

//             'area_variation_context' => [

//                 'area_variation_id' => $this->id,

//                 'plot_id' => $plot?->id,

//                 'project_id' => $plot?->project_id,
//                 'project_name' => $plot?->project?->project_name,

//                 'block_id' => $plot?->block_id,
//                 'block_name' => $plot?->block?->block_name,

//                 'street_id' => $plot?->street_id,
//                 'street_name' => $plot?->street?->street_name,

//                 'plot_number' => $plot?->plot_number,

//                 'property_type_id' => $plot?->property_type_id,
//                 'property_type_name' => $plot?->propertyType?->name,

//                 'size_id' => $plot?->size_id,
//                 'size_title' => $plot?->size?->size_title,

//                 'measured_by' => $this->measured_by,
//                 'measured_by_name' => $measuredByName,

//             ],

//         ]);
//     }


//     /*
//     |--------------------------------------------------------------------------
//     | Relationships
//     |--------------------------------------------------------------------------
//     */

//     public function plot()
//     {
//         return $this->belongsTo(Plot::class);
//     }
// }