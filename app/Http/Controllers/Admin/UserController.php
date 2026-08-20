<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
// for password change
use Illuminate\Support\Facades\Auth;


// class UserController extends Controller
class UserController extends Controller implements HasMiddleware
{
    // public function __construct()
    // {
    //     // $this->middleware(['role:admin']);
    //     $this->middleware('role:admin');
    //     // $this->middleware('permission:manage users');
    // }

    public static function middleware(): array
    {
        return [
            // new Middleware('role:admin|super-admin'),
            new Middleware('role:admin'),       
        ];
    }

    // public function __construct()
    // {
    //     $this->middleware('permission:manage users');
    // }

    public function index()
    {
        $permissions = Permission::all();

        $users = User::with('roles')->get();
        return view('admin.users.index', compact(['users','permissions']));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', 'User Created');
    }
    
// old store method
    // public function store(Request $request)
    // {
    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => bcrypt('12345678'),
    //     ]);

    //     $user->assignRole($request->role);

    //     return redirect()->route('admin.users.index')
    //         ->with('success', 'User Created');
    // }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->only('name','email'));
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User Updated');
    }
    // change password 
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        // check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect');
        }

        // update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password changed successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User Deleted');
    }
}
