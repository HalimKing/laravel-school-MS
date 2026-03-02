<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions
     */
    public function index()
    {
        $permissions = Permission::all()->groupBy('category')->sortKeys();

        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Show create permission form
     */
    public function create()
    {
        $categories = [
            'user' => 'User Management',
            'role' => 'Role & Permission',
            'attendance' => 'Attendance',
            'academic' => 'Academic',
            'fee' => 'Fee Management',
            'report' => 'Reports',
            'setting' => 'Settings',
        ];

        return view('admin.permissions.create', compact('categories'));
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name|max:100',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$validated['name']}' created successfully!");
    }

    /**
     * Show edit permission form
     */
    public function edit(Permission $permission)
    {
        $categories = [
            'user' => 'User Management',
            'role' => 'Role & Permission',
            'attendance' => 'Attendance',
            'academic' => 'Academic',
            'fee' => 'Fee Management',
            'report' => 'Reports',
            'setting' => 'Settings',
        ];

        return view('admin.permissions.edit', compact('permission', 'categories'));
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name,' . $permission->id,
            'category' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        $permission->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$permission->name}' updated successfully!");
    }

    /**
     * Delete the specified permission
     */
    public function destroy(Permission $permission)
    {
        if ($permission->roles()->count() > 0) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'Cannot delete permission assigned to roles!');
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully!');
    }

    /**
     * Bulk create permissions
     */
    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'actions' => 'required|string',
        ]);

        $actions = array_filter(array_map('trim', explode(',', $validated['actions'])));
        $created = [];

        foreach ($actions as $action) {
            $permissionName = strtolower($validated['category']) . '.' . strtolower($action);

            if (!Permission::where('name', $permissionName)->exists()) {
                Permission::create([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                    'category' => $validated['category'],
                ]);
                $created[] = $permissionName;
            }
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Created permissions: ' . implode(', ', $created));
    }
}
