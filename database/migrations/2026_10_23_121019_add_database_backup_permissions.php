<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            'database-backup.view',
            'database-backup.create',
            'database-backup.download',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $adminRole = Role::where('name', 'admin')
            ->where('guard_name', 'web')
            ->first();

        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'database-backup.view',
            'database-backup.create',
            'database-backup.download',
        ];

        $adminRole = Role::where('name', 'admin')
            ->where('guard_name', 'web')
            ->first();

        if ($adminRole) {
            $adminRole->revokePermissionTo($permissions);
        }

        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)
                ->where('guard_name', 'web')
                ->first();

            if ($permission && $permission->roles()->count() === 0) {
                $permission->delete();
            }
        }
    }
};
