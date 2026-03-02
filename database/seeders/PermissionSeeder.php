<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Define permissions by category
        $permissions = [
            'user' => [
                'user.create' => 'Create user',
                'user.read' => 'Read user',
                'user.update' => 'Update user',
                'user.delete' => 'Delete user',
                'user.export' => 'Export users',
            ],
            'role' => [
                'role.create' => 'Create role',
                'role.read' => 'Read role',
                'role.update' => 'Update role',
                'role.delete' => 'Delete role',
            ],
            'permission' => [
                'permission.create' => 'Create permission',
                'permission.read' => 'Read permission',
                'permission.update' => 'Update permission',
                'permission.delete' => 'Delete permission',
            ],
            'attendance' => [
                'attendance.create' => 'Take attendance',
                'attendance.read' => 'View attendance',
                'attendance.update' => 'Update attendance',
                'attendance.delete' => 'Delete attendance',
                'attendance.report' => 'Generate attendance reports',
                'attendance.analytics' => 'View attendance analytics',
            ],
            'academic' => [
                'academic.create' => 'Create academic record',
                'academic.read' => 'Read academic record',
                'academic.update' => 'Update academic record',
                'academic.delete' => 'Delete academic record',
            ],
            'fee' => [
                'fee.create' => 'Create fee',
                'fee.read' => 'Read fee',
                'fee.update' => 'Update fee',
                'fee.delete' => 'Delete fee',
                'fee.collect' => 'Collect fees',
                'fee.report' => 'Generate fee reports',
            ],
            'report' => [
                'report.create' => 'Create report',
                'report.read' => 'Read report',
                'report.update' => 'Update report',
                'report.delete' => 'Delete report',
                'report.export' => 'Export report',
            ],
            'setting' => [
                'setting.create' => 'Create setting',
                'setting.read' => 'Read setting',
                'setting.update' => 'Update setting',
                'setting.delete' => 'Delete setting',
            ],
        ];

        // Create all permissions
        foreach ($permissions as $category => $perms) {
            foreach ($perms as $name => $description) {
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                    [
                        'category' => $category,
                        'description' => $description,
                    ]
                );
            }
        }

        // Create root/super-admin role with all permissions
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'web'],
            ['description' => 'Super Administrator with all permissions']
        );
        $superAdminRole->syncPermissions(Permission::all());

        // Create admin role
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['description' => 'Administrator']
        );
        $adminPermissions = Permission::whereIn('category', ['user', 'role', 'permission', 'setting'])->pluck('id');
        $adminRole->syncPermissions($adminPermissions);

        // Create teacher role
        $teacherRole = Role::firstOrCreate(
            ['name' => 'teacher', 'guard_name' => 'web'],
            ['description' => 'Teacher']
        );
        $teacherPermissions = Permission::whereIn('name', [
            'attendance.create',
            'attendance.read',
            'attendance.report',
            'academic.read',
            'report.read',
        ])->pluck('id');
        $teacherRole->syncPermissions($teacherPermissions);

        // Create student role
        $studentRole = Role::firstOrCreate(
            ['name' => 'student', 'guard_name' => 'web'],
            ['description' => 'Student']
        );
        $studentPermissions = Permission::whereIn('name', [
            'attendance.read',
            'academic.read',
            'report.read',
        ])->pluck('id');
        $studentRole->syncPermissions($studentPermissions);

        // Create parent role
        $parentRole = Role::firstOrCreate(
            ['name' => 'parent', 'guard_name' => 'web'],
            ['description' => 'Parent/Guardian']
        );
        $parentPermissions = Permission::whereIn('name', [
            'attendance.read',
            'academic.read',
            'report.read',
        ])->pluck('id');
        $parentRole->syncPermissions($parentPermissions);

        // Create accountant role
        $accountantRole = Role::firstOrCreate(
            ['name' => 'accountant', 'guard_name' => 'web'],
            ['description' => 'Accountant']
        );
        $accountantPermissions = Permission::whereIn('category', ['fee', 'report'])->pluck('id');
        $accountantRole->syncPermissions($accountantPermissions);

        // Clear cache again
        app()['cache']->forget('spatie.permission.cache');

        $this->command->info('Permissions and roles created successfully!');
    }
}
