<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;


class PermissionController extends Controller
{
    public function index()
    {
        // $permissions = Permission::all();
        $permissions = Permission::orderBy('name')->get();
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        Permission::create([
            'name' => $request->name
        ]);

        // Permission::create([
        //     'name' => $request->name
        // ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission updated');
        // return redirect()->route('admin.permissions.index');
    }

    public function destroy(Permission $permission)
    {
        $protected = [
            'user.view',
            'role.view',
            'permission.view',
        ];

        if (in_array($permission->name, $protected)) {
            abort(403, 'This permission is protected.');
            // return back()->with('error', 'This permission cannot be deleted');

        }
        
        $permission->roles()->detach();
        $permission->delete();

        return redirect()->back()->with('success', 'Permission deleted successfully');
    }

    public function edit(Permission $permission){   // 👈 MUST exist
        
        return view('admin.permissions.edit', compact('permission'));
    }
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully');
    }



}
