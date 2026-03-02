# Roles & Permissions System Documentation

## Overview

The school management system includes a comprehensive **Role-Based Access Control (RBAC)** system powered by **Spatie Laravel Permission** package. This system allows administrators to create roles, define permissions, and assign them to users.

## Features

- **Role Management**: Create, edit, and delete roles
- **Permission Management**: Define granular permissions organized by categories
- **User Role Assignment**: Assign one or multiple roles to users
- **Permission Verification**: Check user permissions before performing actions
- **Bulk Permission Creation**: Create multiple permissions at once
- **Role-Based Dashboard**: Filter users and manage roles efficiently

## Core Concepts

### Permissions

A **permission** represents a specific action that can be performed in the system. Permissions are organized into categories:

- **User Management**: Create, read, update, delete users and export user lists
- **Role & Permission**: Manage roles, permissions, and access control
- **Attendance**: Record attendance, view reports, and analytics
- **Academic**: Manage academic records and settings
- **Fee Management**: Collect fees, manage fee categories, and generate reports
- **Reports**: Generate and export various reports
- **Settings**: Configure system settings

**Permission Naming Convention**:

- Format: `module.action`
- Examples: `user.create`, `attendance.read`, `fee.collect`

### Roles

A **role** is a collection of permissions that can be assigned to users. Pre-defined roles include:

#### Super Admin

- **Description**: Administrator with all permissions
- **Access**: All features and settings
- **Use Case**: System owner/CTO

#### Admin

- **Description**: Administrator with management permissions
- **Access**: User, role, permission, and settings management
- **Use Case**: School administrator

#### Teacher

- **Description**: Educational staff with limited access
- **Access**: Attendance taking, academic records, reports
- **Use Case**: Classroom teachers

#### Student

- **Description**: Student with read-only access
- **Access**: Attendance records, academic information, reports
- **Use Case**: Enrolled students

#### Parent/Guardian

- **Description**: Parent with limited student information access
- **Access**: Student attendance, academic records, reports
- **Use Case**: Parent/guardians of students

#### Accountant

- **Description**: Finance staff
- **Access**: Fee management, financial reports
- **Use Case**: Finance department staff

## Usage Guide

### Managing Roles

#### View All Roles

1. Navigate to **Settings → Role & Permission Management**
2. View all created roles with permission counts and user assignments

#### Create a New Role

1. Click **Add Role** button
2. Enter role name (e.g., "examiner")
3. Add optional description
4. Select desired permissions from all categories
5. Use **Select all for category** checkbox to quickly select all permissions in a category
6. Click **Create Role**

#### Edit a Role

1. Click the edit icon next to the role
2. Update role details or permissions
3. Click **Update Role**
4. Changes apply immediately to all users with this role

#### Delete a Role

1. Click the delete icon next to the role
2. Note: Cannot delete roles with assigned users
3. Confirm deletion
4. Role is permanently removed

### Managing Permissions

#### View All Permissions

1. Navigate to **Settings → Role & Permission Management → Permissions**
2. Permissions are organized by category
3. Shows permission name and optional description

#### Create a Single Permission

1. Click **Add Permission** tab
2. Enter permission name (use dot notation: `module.action`)
3. Select category
4. Add optional description
5. Click **Create Permission**

#### Bulk Create Permissions

1. Click **Add Permission → Bulk Create**
2. Select category
3. Enter actions separated by commas: `create, read, update, delete, export`
4. System creates permissions automatically as `category.action`
5. Example: If category is "report" and actions are "create, export",
   creates: `report.create`, `report.export`

#### Edit a Permission

1. Click the edit icon next to the permission
2. Update permission details
3. Click **Update Permission**

#### Delete a Permission

1. Click the delete icon next to the permission
2. Note: Cannot delete permissions assigned to roles
3. Confirm deletion

### Managing User Roles

#### View All Users with Roles

1. Navigate to **Settings → User Role Assignment**
2. See all users and their assigned roles
3. Filter or search for specific users

#### Assign Roles to User

1. Click the edit icon next to a user
2. View user information and current permissions
3. Check/uncheck roles to assign or remove
4. Visual feedback shows which roles are assigned
5. Click **Update Roles**
6. Changes apply immediately

#### Check User Permissions

In the "Assign Roles" view:

- **Current Roles** section shows directly assigned roles
- **Direct Permissions** section shows any directly assigned permissions
- **All Permissions** are inherited from assigned roles

## API Usage in Code

### Check User Role

```php
// Single role check
if (auth()->user()->hasRole('teacher')) {
    // User is a teacher
}

// Multiple roles (OR)
if (auth()->user()->hasAnyRole(['teacher', 'admin'])) {
    // User is either a teacher or admin
}

// All roles check
if (auth()->user()->hasAllRoles(['teacher', 'admin'])) {
    // User has both teacher and admin roles
}
```

### Check User Permission

```php
// Single permission check
if (auth()->user()->can('attendance.create')) {
    // User can take attendance
}

// Direct method
if (auth()->user()->hasPermissionTo('attendance.create')) {
    // User can take attendance
}
```

### Assign/Remove Roles

```php
// Assign single role
$user->assignRole('teacher');

// Assign multiple roles
$user->assignRole(['teacher', 'examiner']);

// Sync roles (replace all)
$user->syncRoles(['teacher', 'examiner']);

// Remove role
$user->removeRole('teacher');
```

### Assign/Remove Permissions (Direct)

```php
// Assign direct permission (bypasses roles)
$user->givePermissionTo('attendance.create');

// Remove direct permission
$user->revokePermissionTo('attendance.create');

// Sync permissions
$user->syncPermissions(['attendance.create', 'attendance.read']);
```

## Protected Routes with Middleware

### Route Protection Examples

```php
// Single role requirement
Route::get('admin/dashboard', [ControllerClass::class, 'action'])
    ->middleware('role:admin');

// Multiple roles (OR)
Route::get('management', [ControllerClass::class, 'action'])
    ->middleware('role:admin,manager');

// Single permission requirement
Route::post('attendance/create', [ControllerClass::class, 'store'])
    ->middleware('permission:attendance.create');

// Multiple permissions (OR)
Route::get('reports', [ControllerClass::class, 'view'])
    ->middleware('permission:report.read,report.export');
```

## Blade Template Usage

### Check Role in Views

```blade
@if(auth()->user()->hasRole('admin'))
    <a href="{{ route('admin.panel') }}">Admin Panel</a>
@endif

@if(auth()->user()->hasAnyRole(['admin', 'teacher']))
    <span class="badge">Staff</span>
@endif
```

### Check Permission in Views

```blade
@if(auth()->user()->can('user.create'))
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        Create User
    </a>
@endif

@can('attendance.delete')
    <button class="btn btn-danger">Delete Attendance</button>
@endcan
```

## Default Permissions

The system comes with pre-defined permissions across 8 categories:

### User Management (6 permissions)

- user.create, user.read, user.update, user.delete, user.export

### Role & Permission (6 permissions)

- role.create, role.read, role.update, role.delete
- permission.create, permission.read, permission.update, permission.delete

### Attendance (6 permissions)

- attendance.create, attendance.read, attendance.update, attendance.delete
- attendance.report, attendance.analytics

### Academic (4 permissions)

- academic.create, academic.read, academic.update, academic.delete

### Fee Management (6 permissions)

- fee.create, fee.read, fee.update, fee.delete, fee.collect, fee.report

### Reports (5 permissions)

- report.create, report.read, report.update, report.delete, report.export

### Settings (4 permissions)

- setting.create, setting.read, setting.update, setting.delete

## Default Roles & Permissions

### Super Admin

- All permissions

### Admin

- All permissions in: User Management, Role & Permission, Settings

### Teacher

- attendance.create, attendance.read, attendance.report
- academic.read
- report.read

### Student

- attendance.read
- academic.read
- report.read

### Parent

- attendance.read
- academic.read
- report.read

### Accountant

- All permissions in: Fee Management, Reports

## Best Practices

1. **Principle of Least Privilege**: Assign only necessary permissions to roles
2. **Clear Role Hierarchy**: Define roles based on organizational structure
3. **Regular Audits**: Review role assignments quarterly
4. **Documentation**: Document custom roles and their purposes
5. **Avoid Direct Permissions**: Use roles instead of directly assigning permissions to users
6. **Meaningful Names**: Use descriptive names for custom roles and permissions
7. **Category Organization**: Place related permissions in the same category
8. **Testing**: Test permission restrictions before deploying to production

## Troubleshooting

### User Cannot Access Feature

1. Check if user has required role
2. Verify role has required permission
3. Confirm permission is assigned
4. Check middleware is properly configured

### Permission Not Found

1. Verify permission name spelling
2. Check permission was created
3. Confirm permission is assigned to role

### Role Not Applying Immediately

1. Clear cache: `php artisan cache:clear`
2. Clear Spatie cache: `php artisan permission:cache-reset`

## Cache Management

Spatie Permission caches role and permission data. To refresh cache:

```bash
# Clear permission cache
php artisan permission:cache-reset

# Clear all application cache
php artisan cache:clear
```

## Additional Resources

- [Spatie Laravel Permission Documentation](https://spatie.be/docs/laravel-permission/)
- [Laravel Authentication](https://laravel.com/docs/authentication)
- [Laravel Authorization](https://laravel.com/docs/authorization)
