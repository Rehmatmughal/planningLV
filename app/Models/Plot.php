<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use App\Models\PropertyType;


class Plot extends Model
{
    use HasFactory, LogsActivity;

    // use HasFactory;
    use SoftDeletes;

    // protected $fillable = ['project_id'];
    protected $fillable = [
        'project_id',
        'pid_lv',
        'block_id',
        'street_id',
        'plot_number',
        'size_id',
        'property_type_id',
        'category_id',
        'numbering_type',
        'remarks'
    ];

    // protected $guarded =[];
    // protected $fillable = ['project_id','block_id','street_id','plot_number','size','numbering_type','remarks','created_at','updated_at'];

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    // public function propertyType()
    // {
    //     return $this->belongsTo(PropertyType::class, 'property_type_id');
    // }

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


    public function size() 
    { 
        return $this->belongsTo(PlotSize::class, 'size_id'); 
    }


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


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('plot')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function (string $eventName) {
                return "Plot has been {$eventName}";
            });
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (auth()->check()) {
            $activity->causer_id = auth()->id();
        }

        // Load required relationships for readable log details
        $this->loadMissing([
            'project',
            'block',
            'street',
            'propertyType',
            'size',
        ]);

        $activity->properties = $activity->properties->merge([
            'plot_context' => [
                'project_id' => $this->project_id,
                'project_name' => $this->project?->name,

                'block_id' => $this->block_id,
                'block_name' => $this->block?->name,

                'street_id' => $this->street_id,
                'street_name' => $this->street?->name,

                'plot_id' => $this->id,
                'plot_number' => $this->plot_number,

                'property_type_id' => $this->property_type_id,
                'property_type' => $this->propertyType?->name,

                'size_id' => $this->size_id,
                'size' => $this->size?->name,
            ],
        ]);
    }
    
}
