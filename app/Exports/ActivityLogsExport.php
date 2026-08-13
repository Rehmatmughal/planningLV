<?php

namespace App\Exports;

use Spatie\Activitylog\Models\Activity;
use Maatwebsite\Excel\Concerns\FromCollection;

class ActivityLogsExport implements FromCollection
{
    public function collection()
    {
        return Activity::with('causer')
            ->latest()
            ->get()
            ->map(function ($activity) {
                return [
                    'Date' => $activity->created_at,
                    'User' => optional($activity->causer)->name,
                    'Event' => $activity->event,
                    'Model' => class_basename($activity->subject_type),
                    'Description' => $activity->description,
                ];
            });
    }
}
