<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $role = Role::create([
            'name' => $request->name
        ]);

        $role->syncPermissions($request->permissions);

        return redirect()->route('admin.roles.index');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact(
            'role',
            'permissions',
            'rolePermissions'
        ));
    }

    public function update(Request $request, Role $role)
    {
        $role->update([
            'name' => $request->name
        ]);

        $role->syncPermissions($request->permissions);

        return redirect()->route('admin.roles.index')
            ->with('success','Role updated');
    }
    public function destroy(Role $role)
    {
        
        // 1. Protect core roles
        if (in_array($role->name, ['admin', 'super-admin'])) {
            // abort(403);
            return back()->with('error', 'This role cannot be deleted');
        }

        // // 2. Check users attached
        // if ($role->users()->exists()) {
        //     return back()->with('error', 'Role assigned to users');
        // }
        DB::table('model_has_roles')->where('role_id', $role->id)->delete();

        // detach permissions
        DB::table('role_has_permissions')->where('role_id', $role->id)->delete();

        // // 3. Detach permissions
        // $role->permissions()->detach();
        // $role->syncPermissions([]);
        // $role->users()->detach(); // agar many-to-many hai


        // 4. Delete role
        $role->delete();

        return back()->with('success', 'Role deleted');
    }

}
