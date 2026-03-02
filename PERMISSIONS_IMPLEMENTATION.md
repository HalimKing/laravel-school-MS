# Permission Implementation Guide

## Overview

This guide explains how permissions are enforced throughout the School Management System. The system uses **Spatie Laravel Permission** package to manage role-based access control (RBAC).

## Permission Enforcement Levels

### 1. Route-Level Permission Middleware

Routes are protected using the `can:` middleware. This prevents unauthorized access before the controller is even executed.

#### Examples:

```php
// User management routes
Route::middleware('can:user.read')->resource('users', UserController::class, ['only' => ['index', 'show']]);
Route::middleware('can:user.create')->get('users/create', [UserController::class, 'create'])->name('users.create');
Route::middleware('can:user.delete')->delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

// Attendance routes
Route::middleware('can:attendance.create')->get('take-attendance', [AttendanceController::class, 'create'])->name('create');
Route::middleware('can:attendance.read')->get('class-report', [AttendanceController::class, 'classReport'])->name('class-report');

// Fee management
Route::middleware('can:fee.collect')->post('collect-fees', [CollectFeeController::class, 'store'])->name('collect-fees.store');
```

#### Multiple Permission Check (OR logic):

```php
// User can access if they have ANY of these permissions
Route::middleware('can:user.read|role.read|permission.read')->group(function () {
    // Routes here...
});
```

### 2. View-Level Permission Checks

User interface elements (buttons, links, entire sections) are hidden based on permissions using the `@can` directive.

#### Examples:

**Hide action buttons in user management:**

```blade
@can('user.update')
<a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
    Edit User
</a>
@endcan

@can('user.delete')
<form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
    <button type="submit" class="btn btn-danger">Delete User</button>
</form>
@endcan
```

**Hide sidebar menu items:**

```blade
@if($hasMenuPermission)
<li class="nav-item">
    <a href="{{ route('admin.users.index') }}" class="nav-link">Users</a>
</li>
@endif
```

### 3. Controller-Level Authorization (Optional)

For more complex logic, you can add authorization checks in controllers using Gates or Policies.

#### Example:

```php
public function edit(User $user)
{
    // Check permission before allowing edit
    if (!auth()->user()->can('user.update')) {
        abort(403, 'Unauthorized action.');
    }

    return view('admin.users.edit', compact('user'));
}
```

## Permission Categories

### User Management

- `user.create` - Create new users
- `user.read` - View users
- `user.update` - Edit users
- `user.delete` - Delete users
- `user.export` - Export user data

### Role & Permission Management

- `role.create` - Create roles
- `role.read` - View roles
- `role.update` - Edit roles
- `role.delete` - Delete roles
- `permission.create` - Create permissions
- `permission.read` - View permissions
- `permission.update` - Edit permissions
- `permission.delete` - Delete permissions

### Attendance

- `attendance.create` - Take attendance
- `attendance.read` - View attendance
- `attendance.update` - Update attendance
- `attendance.delete` - Delete attendance
- `attendance.report` - Generate attendance reports
- `attendance.analytics` - View attendance analytics

### Academic Management

- `academic.create` - Create academic records
- `academic.read` - View academic records
- `academic.update` - Update academic records
- `academic.delete` - Delete academic records

### Fee Management

- `fee.create` - Create fees
- `fee.read` - View fees
- `fee.update` - Update fees
- `fee.delete` - Delete fees
- `fee.collect` - Collect fees from students
- `fee.report` - Generate financial reports

### Reports

- `report.create` - Create reports
- `report.read` - View reports
- `report.update` - Update reports
- `report.delete` - Delete reports
- `report.export` - Export reports

### Settings

- `setting.create` - Create settings
- `setting.read` - Access settings
- `setting.update` - Update settings
- `setting.delete` - Delete settings

## Default Roles & Permissions

### Super Admin

- **Permissions**: ALL permissions
- **Use Case**: System administrator with complete control
- **Access**: All features

### Admin

- **Permissions**: User, Role, Permission, Setting management
- **Use Case**: School administrator
- **Access**: User management, Role/Permission setup, System settings

### Teacher

- **Permissions**:
    - `attendance.create` - Can take attendance
    - `attendance.read` - Can view attendance
    - `attendance.report` - Can generate attendance reports
    - `academic.read` - Can view academic records
    - `report.read` - Can view reports
- **Use Case**: Classroom teachers
- **Access**: Limited to attendance and academic viewing

### Student

- **Permissions**:
    - `attendance.read` - Can view own attendance
    - `academic.read` - Can view own academic records
    - `report.read` - Can view reports
- **Use Case**: Enrolled students
- **Access**: Read-only access to personal data

### Parent/Guardian

- **Permissions**: Same as Student (read-only)
- **Use Case**: Parents/guardians
- **Access**: Can view child's records (if future parent-student relations added)

### Accountant

- **Permissions**: Fee and financial report management
    - `fee.create`, `fee.read`, `fee.update`, `fee.delete`
    - `fee.collect` - Can receive payments
    - `fee.report` - Can generate financial reports
    - `report.read`, `report.export`
- **Use Case**: Finance department
- **Access**: Fee and financial management only

## How to Add New Permissions

### Via Admin Panel:

1. Go to Settings → Role & Permission Management → Permissions
2. Click "Add Permission"
3. Enter permission name (format: `category.action`)
4. Select category
5. Assign to roles

### Via Seeder:

Edit `database/seeders/PermissionSeeder.php`:

```php
$permissions = [
    'new_category' => [
        'new_category.create' => 'Create something',
        'new_category.read' => 'Read something',
        'new_category.update' => 'Update something',
        'new_category.delete' => 'Delete something',
    ],
];
```

Then run: `php artisan db:seed --class=PermissionSeeder`

## Testing Permissions

### Check User Permission:

```php
// In controller or view
if (auth()->user()->can('user.create')) {
    // User has permission
}

// Alternative
if (auth()->user()->hasPermissionTo('user.create')) {
    // User has permission
}
```

### Check User Role:

```php
if (auth()->user()->hasRole('admin')) {
    // User is admin
}

if (auth()->user()->hasAnyRole(['admin', 'super-admin'])) {
    // User is admin or super-admin
}
```

### In Blade Templates:

```blade
@can('user.create')
    <!-- Show create button -->
@endcan

@canany(['user.read', 'user.create'])
    <!-- Show if user has any of these permissions -->
@endcanany

@role('admin')
    <!-- Show to admins only -->
@endrole

@hasrole(['admin', 'super-admin'])
    <!-- Show to admin or super-admin -->
@endhasrole
```

## Permission Caching

Permissions are cached for performance. If you modify permissions directly in the database or via seeder, clear the cache:

```bash
php artisan cache:clear
php artisan permission:cache-reset
```

## Authorization Failures

### When Access is Denied:

1. Route-level: User is redirected to `/403` (Access Denied page)
2. View-level: UI elements are simply hidden (no error shown)
3. Controller-level: Manual `abort(403)` triggers Access Denied page

### Custom 403 Page:

The system displays a user-friendly 403 page at `resources/views/errors/403.blade.php` showing:

- User's current roles
- Back to dashboard button
- Go back option

## Best Practices

### 1. Use Permission Middleware on Routes

Always protect routes with appropriate `can:` middleware rather than relying solely on controller checks.

```php
// Good
Route::middleware('can:user.create')->post('/users', [UserController::class, 'store']);

// Less ideal
Route::post('/users', [UserController::class, 'store']); // Only checks in controller
```

### 2. Hide UI Before Routes

Use `@can` in views to hide buttons/links for users without permission. This prevents confusing error pages.

```blade
<!-- User won't see the button if they can't click it -->
@can('user.delete')
    <button>Delete</button>
@endcan
```

### 3. Use Permission Names Consistently

Use dot notation: `module.action`

```php
// Good
'user.create', 'attendance.read', 'fee.collect'

// Avoid
'create_user', 'can_read_attendance', 'Collect Fees'
```

### 4. Test Permission Changes

After modifying permissions or roles:

```bash
php artisan cache:clear
php artisan permission:cache-reset
```

Then login with test accounts to verify access.

### 5. Document Custom Permissions

If you add new permissions, document them here and in the codebase.

## Troubleshooting

### Permission Check Not Working

1. **Clear cache:**

    ```bash
    php artisan cache:clear
    php artisan permission:cache-reset
    ```

2. **Verify seeder ran:**

    ```bash
    php artisan db:seed --class=PermissionSeeder
    ```

3. **Check user roles:**
   Go to user profile and verify roles are assigned

4. **Check role permissions:**
   Go to Role & Permission Management and verify role has the permission

### Unwanted Access Allowed

1. Verify route middleware is set correctly
2. Check that user's role has permission
3. Clear cache
4. Check for conflicting `@can` directives in parent views

### Permission Display Issues

1. Verify `@can` syntax is correct
2. Check permission name spelling
3. Ensure user is authenticated (use `auth()->check()` if needed)

## Security Tips

1. **Never trust frontend:** Always validate permissions on backend routes
2. **Use middleware:** Prefer route middleware to controller checks
3. **Principle of least privilege:** Assign only necessary permissions
4. **Audit logs:** Consider adding audit logging for sensitive operations
5. **Regular review:** Periodically review who has which permissions

## File References

- **Routes:** `routes/web.php` - Permission middleware on all protected routes
- **Permissions:** `database/seeders/PermissionSeeder.php` - Permission definitions
- **Roles:** `database/seeders/PermissionSeeder.php` - Role assignments
- **Views:** `resources/views/` - @can directives throughout
- **Error page:** `resources/views/errors/403.blade.php` - Access denied display
- **Sidebar:** `resources/views/partials/app-sidebar.blade.php` - Menu item permissions
