<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of all users
     */
    public function index()
    {
        $users = User::with('roles')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in the database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'numeric',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        // Assign roles - only sync if roles were selected and are valid
        if (!empty($validated['roles'])) {
            // Filter to keep only valid numeric role IDs and verify they exist
            $roleIds = array_values(array_filter(array_map('intval', $validated['roles'] ?? [])));

            // Verify all role IDs exist before syncing
            $existingRoles = Role::whereIn('id', $roleIds)->pluck('id')->toArray();

            if (!empty($existingRoles)) {
                $user->syncRoles($existingRoles);
            }
        }

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', "User '{$user->name}' created successfully!");
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load('roles');

        // Get all permissions for user through roles - safely collect them
        $allPermissions = $user->getAllPermissions();
        $permissions = $allPermissions ? collect($allPermissions) : collect([]);

        return view('admin.users.show', compact('user', 'permissions'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles()->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified user in the database
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'numeric',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->update(['password' => bcrypt($validated['password'])]);
        }

        // Sync roles - only sync if roles data is provided and valid
        if (array_key_exists('roles', $validated) && !empty($validated['roles'])) {
            // Filter to keep only valid numeric role IDs and verify they exist
            $roleIds = array_values(array_filter(array_map('intval', $validated['roles'] ?? [])));

            // Verify all role IDs exist before syncing
            $existingRoles = Role::whereIn('id', $roleIds)->pluck('id')->toArray();

            if (!empty($existingRoles)) {
                $user->syncRoles($existingRoles);
            }
        }

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', "User '{$user->name}' updated successfully!");
    }

    /**
     * Remove the specified user from the database
     */
    public function destroy(User $user)
    {
        // Prevent deleting the last admin user
        if ($user->hasRole('super-admin') && User::whereHas('roles', function ($q) {
            $q->where('name', 'super-admin');
        })->count() === 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete the last super-admin user!');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$userName}' deleted successfully!");
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => bcrypt($validated['password']),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }

    /**
     * Reset user password
     */
    public function resetPassword(User $user)
    {
        $tempPassword = str_random(12);

        $user->update([
            'password' => bcrypt($tempPassword),
        ]);

        return back()->with('success', "Password reset to: $tempPassword (Please share with the user securely)");
    }

    /**
     * Activate/Deactivate user
     */
    public function toggleActive(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User '{$user->name}' has been $status!");
    }
}
