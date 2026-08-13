<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;


class Plot extends Model
{
    use HasFactory, LogsActivity;

    // use HasFactory;
    use SoftDeletes;

    // protected $fillable = ['project_id'];
    // protected $fillable = [
    //     'project_id',
    //     'pid_lv',
    //     'block_id',
    //     'street_id',
    //     'plot_number',
    //     'size_id',
    //     'category_id',
    //     'numbering_type',
    //     'remarks'
    // ];

    protected $guarded =[];
    // protected $fillable = ['project_id','block_id','street_id','plot_number','size','numbering_type','remarks','created_at','updated_at'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
 
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function street()
    {
        return $this->belongsTo(Street::class);
    }

    // public function plotsize() 
    // { 
    //     return $this->belongsTo(PlotSize::class, 'size_id'); 
    // }

    // public function psize() 
    // { 
    //     return $this->belongsTo(PlotSize::class); 
    // }
    public function size() 
    { 
        return $this->belongsTo(PlotSize::class, 'size_id'); 
    }

    // public function plotsizeav() 
    // { 
    //     return $this->belongsTo(PlotSize::class); 
    // }

    public function category()
    {
        return $this->belongsTo(PlotCategoryType::class);
    }
    
    // new added

    // Relationships

    public function developmentStatus() {
        return $this->hasOne(DevelopmentStatus::class);
    }

    public function coordinates(){
        return $this->hasOne(PlotCoordinate::class);
    }

    public function lopStatus() {
        return $this->hasOne(LopStatus::class);
    }

    public function mortgageStatus() {
        return $this->hasOne(MortgageStatus::class);
    }

    public function possessionStatus() {
        return $this->hasOne(PossessionStatus::class);
    }

    public function areaVariations() {
        return $this->hasMany(AreaVariation::class);
    }

    public function latestAreavariation(){
        return $this->hasone(AreaVariation::class)->latestOfMany();
    }

    public function plotArea() {
        return $this->belongsTo(Plotsize::class);
    }

    // loging system
    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->useLogName('plot')
    //         ->logFillable()
    //         ->logOnlyDirty()
    //         ->dontSubmitEmptyLogs()
    //         ->logExcept(['deleted_at'])
    //         ->setDescriptionForEvent(fn(string $eventName) => 
    //             "Plot has been {$eventName}"
    //         );
    // }

    // public function tapActivity(Activity $activity, string $eventName)
    // {
    //     if (auth()->check()) {
    //         $activity->causer_id = auth()->id();
    //     }

    //     $activity->properties = $activity->properties->merge([
    //         'project_id' => $this->project_id,
    //         'block_id' => $this->block_id,
    //         'street_id' => $this->street_id,
    //     ]);
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Development_status')
            // ->logFillable()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => 
                "Plot Status has been {$eventName}"
            );
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (auth()->check()) {
            $activity->causer_id = auth()->id();
        }

        $activity->properties = $activity->properties->merge([
            'project_id' => $this->project_id,
            'projet_name' => $this->project_name,
            'block_name' => $this->block_name,
            'block_id' => $this->block_id,
            'street_id' => $this->street_id,
        ]);
    }

}
