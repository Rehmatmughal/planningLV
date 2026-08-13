<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        /**
         * ROLES
         */

        // $superAdmin = Role::create(['name' => 'super-admin']);
        // $admin      = Role::create(['name' => 'admin']);
        // $staff      = Role::create(['name' => 'staff']);
        // $guest      = Role::create(['name' => 'guest']);

        $superAdmin = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $staff = Role::firstOrCreate([
            'name' => 'staff',
            'guard_name' => 'web',
        ]);

        $guest = Role::firstOrCreate([
            'name' => 'guest',
            'guard_name' => 'web',
        ]);

        /**
         * PERMISSIONS
         */
        $permissions = [
            'dashboard.view',
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            'role.view',
            'role.create',
            'role.edit',
            'role.delete',

            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete',
        ];

        foreach ($permissions as $perm) {
            // Permission::create(['name' => $perm]);
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web',
            ]);
        }

        /**
         * ASSIGN PERMISSIONS
         */
        $admin->givePermissionTo([ 
            'dashboard.view',
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            'role.view',
            'role.create',
            'role.edit',
            'role.delete',

            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete',
        ]);

        $staff->givePermissionTo([
            'dashboard.view',
        ]);

        $guest->givePermissionTo([
            'dashboard.view',
        ]);

        /**
         * USERS (PASSWORD = password)
         */
        $super = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
        ]);
        $super->assignRole('super-admin');

        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $adminUser->assignRole('admin');

        $staffUser = User::create([
            'name' => 'Staff',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
        ]);
        $staffUser->assignRole('staff');

        $guestUser = User::create([
            'name' => 'Guest',
            'email' => 'guest@example.com',
            'password' => Hash::make('password'),
        ]);
        $guestUser->assignRole('guest');
    }
}
