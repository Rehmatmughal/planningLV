<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements HasMiddleware
{
    private const PROTECTED_ROLES = ['admin', 'super-admin'];

    public static function middleware(): array
    {
        return [
            new Middleware(
                'role:admin|super-admin',
                only: [
                    'index',
                    'create',
                    'store',
                    'edit',
                    'update',
                    'destroy',
                ]
            ),
        ];
    }

    private function availableRoles(User $actor): array
    {
        $query = Role::query();

        if (! $actor->hasRole('super-admin')) {
            $query->whereNotIn('name', self::PROTECTED_ROLES);
        }

        return $query->pluck('name')->all();
    }

    private function ensureCanManageTarget(
        Request $request,
        User $target
    ): void {
        $actor = $request->user();

        if (! $actor->hasRole('super-admin')) {
            if (
                $actor->id === $target->id ||
                $target->hasAnyRole(self::PROTECTED_ROLES)
            ) {
                abort(403, 'You are not allowed to manage this account.');
            }
        }
    }

    public function index()
    {
        $permissions = Permission::all();

        $users = User::with('roles')->get();

        return view(
            'admin.users.index',
            compact('users', 'permissions')
        );
    }

    public function create(Request $request)
    {
        $roles = $this->availableRoles($request->user());

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $allowedRoles = $this->availableRoles($request->user());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => [
                'required',
                Rule::in($allowedRoles),
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User Created');
    }

    public function edit(Request $request, User $user)
    {
        $this->ensureCanManageTarget($request, $user);

        $roles = $this->availableRoles($request->user());

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureCanManageTarget($request, $user);

        $actor = $request->user();
        $isEditingOwnAccount = $actor->id === $user->id;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ];

        if ($isEditingOwnAccount) {
            // Super-admin may update their details,
            // but cannot change their own role here.
            $rules['role'] = ['nullable', 'string'];
        } else {
            $rules['role'] = [
                'required',
                Rule::in($this->availableRoles($actor)),
            ];
        }

        $validated = $request->validate($rules);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (! $isEditingOwnAccount) {
            $newRole = $validated['role'];

            if (
                $user->hasRole('super-admin') &&
                $newRole !== 'super-admin'
            ) {
                $superAdminCount = User::role('super-admin')->count();

                if ($superAdminCount <= 1) {
                    abort(
                        403,
                        'The last super-admin cannot be demoted.'
                    );
                }
            }

            $user->syncRoles([$newRole]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User Updated');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required'],
            'new_password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
        ]);

        $user = Auth::user();

        if (! $user) {
            abort(401);
        }

        if (! Hash::check(
            $validated['current_password'],
            $user->password
        )) {
            return back()->with(
                'error',
                'Current password is incorrect'
            );
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return back()->with(
            'success',
            'Password changed successfully'
        );
    }

    public function destroy(Request $request, User $user)
    {
        $this->ensureCanManageTarget($request, $user);

        $actor = $request->user();

        if ($actor->id === $user->id) {
            abort(403, 'You cannot delete your own account here.');
        }

        if (
            $user->hasRole('super-admin') &&
            User::role('super-admin')->count() <= 1
        ) {
            abort(
                403,
                'The last super-admin account cannot be deleted.'
            );
        }

        $user->delete();

        return back()->with('success', 'User Deleted');
    }
}
