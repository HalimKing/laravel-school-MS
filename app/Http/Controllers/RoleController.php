<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index()
    {
        $roles = Role::with('permissions')->paginate(10);
        $permissions = Permission::all()->groupBy('category');

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    /**
     * Show create role form
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('category');

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:100',
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['permissions'])) {
            // Get Permission objects by ID
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('admin.access-control.roles.index')
            ->with('success', "Role '{$role->name}' created successfully!");
    }

    /**
     * Show edit role form
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy('category');
        $rolePermissions = $role->permissions()->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['permissions'])) {
            // Get Permission objects by ID
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.access-control.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully!");
    }

    /**
     * Delete the specified role
     */
    public function destroy(Role $role)
    {
        if ($role->name === 'super-admin') {
            return redirect()->route('admin.access-control.roles.index')
                ->with('error', 'Cannot delete super-admin role!');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('admin.access-control.roles.index')
                ->with('error', 'Cannot delete role with assigned users!');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully!');
    }

    /**
     * Assign permissions to role via AJAX
     */
    public function assignPermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if (!empty($validated['permissions'])) {
            // Get Permission objects by ID
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully!'
        ]);
    }
}
