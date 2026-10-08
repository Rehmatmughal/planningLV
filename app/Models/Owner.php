<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class Owner extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;


    protected $fillable = [
        'owner_name',
        'relative_name',
        'cnic',
        'address',
        'contact_no',
    ];


    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Owner')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(
                fn (string $eventName) =>
                    "Owner {$this->owner_name} has been {$eventName}"
            );
    }


    public function tapActivity(Activity $activity, string $eventName)
    {
        if (auth()->check()) {
            $activity->causer_id = auth()->id();
        }

        $activity->properties = $activity->properties->merge([
            'owner_context' => [
                'owner_id' => $this->id,
                'owner_name' => $this->owner_name,
                'relative_name' => $this->relative_name,
                'cnic' => $this->cnic,
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Possession Cases
    |--------------------------------------------------------------------------
    */

    public function possessionCases()
    {
        return $this->belongsToMany(
            PossessionCase::class,
            'possession_case_owners'
        )
            ->withPivot('address_snapshot')
            ->withTimestamps();
    }
}

// <?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

// class Owner extends Model
// {
//     use HasFactory, SoftDeletes;

//     protected $fillable = [
//         'owner_name',
//         'relative_name',
//         'cnic',
//         'address',
//         'contact_no',
//     ];


//     /*
//     |--------------------------------------------------------------------------
//     | Possession Cases
//     |--------------------------------------------------------------------------
//     */

//     public function possessionCases()
//     {
//         return $this->belongsToMany(
//             PossessionCase::class,
//             'possession_case_owners'
//         )
//          ->withPivot('address_snapshot')
//          ->withTimestamps();
//     }
// }
