<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolePermissionNSeeder extends Seeder
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

        $draftsmanrana = Role::firstOrCreate([
            'name' => 'rananisar',
            'guard_name' => 'web',
        ]);

        $draftsmanali = Role::firstOrCreate([
            'name' => 'aliraza',
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

            'project.view',
            'project.update',
            'project.create',
            'project.edit',
            'project.delete',
            'project.destroy',

            'block.view',
            'block.update',
            'block.create',
            'block.edit',
            'block.delete',
            'block.excel',
            
            'street.view',
            'street.create',
            'street.edit',
            'street.delete',
            'street.update',

            'size.view',
            'size.create',
            'size.update',
            'size.edit',
            'size.delete',
            'size.trashview',
            'size.trashrestore',
            'size.force-delete',
            'size.restore',


            'category.view',
            'category.create',
            'category.edit',
            'category.delete',

            'plot.view',
            'plot.create',
            'plot.update',
            'plot.edit',
            'plot.delete',
            'plot.excel',
            'plot.trashview',
            'plot.restore',
            'plot.force-delete',

            'role.store',

            'areavariation.create',
            'areavariation.view',

            'lop.view',
            'lop.create',
            'lop.update',
            'lop.edit',

            'development.view',
            'development.edit',
            'development.create',
            'development.update',

            'area.view',

            'activity.view'

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

            'project.view',
            'project.update',
            'project.create',
            'project.edit',
            'project.delete',
            'project.destroy',

            'block.view',
            'block.update',
            'block.create',
            'block.edit',
            'block.delete',
            'block.excel',
            
            'street.view',
            'street.create',
            'street.edit',
            'street.delete',
            'street.update',

            'size.view',
            'size.create',
            'size.update',
            'size.edit',
            'size.delete',
            'size.trashview',
            'size.trashrestore',
            'size.force-delete',
            'size.restore',


            'category.view',
            'category.create',
            'category.edit',
            'category.delete',

            'plot.view',
            'plot.create',
            'plot.update',
            'plot.edit',
            'plot.delete',
            'plot.excel',
            'plot.trashview',

            'role.store',

            'areavariation.create',
            'areavariation.view',

            'lop.view',
            'lop.create',
            'lop.update',
            'lop.edit',

            'development.view',
            'development.edit',
            'development.create',
            'development.update',

            'area.view',

            // 'dashboard.view',
            // 'user.view',
            // 'user.create',
            // 'user.edit',
            // 'user.delete',

            // 'role.view',
            // 'role.create',
            // 'role.edit',
            // 'role.delete',

            // 'permission.view',
            // 'permission.create',
            // 'permission.edit',
            // 'permission.delete',
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
