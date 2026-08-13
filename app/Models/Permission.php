<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;


class Permission extends Model
{
    protected static function booted()
    {
        // DELETE se protect
        static::deleting(function ($permission) {

            $protected = [
                'user.view',
                'role.view',
                'permission.view',
            ];

            if (in_array($permission->name, $protected)) {
                abort(403, 'This permission is protected and cannot be deleted.');
            }
        });

        // UPDATE / RENAME se protect
        static::updating(function ($permission) {

            $protected = [
                'user.view',
                'role.view',
                'permission.view',
            ];

            if (in_array($permission->getOriginal('name'), $protected)) {
                abort(403, 'This permission is protected and cannot be updated.');
            }
        });
    }
    
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

}
