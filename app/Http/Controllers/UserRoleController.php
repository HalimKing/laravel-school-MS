<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    /**
     * Display users with their roles
     */
    public function index()
    {
        $users = User::with('roles')->paginate(15);
        $roles = Role::all();

        return view('admin.users.roles', compact('users', 'roles'));
    }

    /**
     * Show role assignment form for a user
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles()->pluck('id')->toArray();

        return view('admin.users.assign-roles', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Assign roles to user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        return redirect()->route('admin.users.roles')
            ->with('success', "Roles assigned to {$user->name} successfully!");
    }

    /**
     * Attach single role to user
     */
    public function attach(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($validated['role_id']);
        $user->assignRole($role);

        return response()->json([
            'success' => true,
            'message' => "Role '{$role->name}' assigned to {$user->name}!"
        ]);
    }

    /**
     * Detach single role from user
     */
    public function detach(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($validated['role_id']);
        $user->removeRole($role);

        return response()->json([
            'success' => true,
            'message' => "Role '{$role->name}' removed from {$user->name}!"
        ]);
    }
}
