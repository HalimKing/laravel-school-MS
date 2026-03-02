# Permission System Quick Reference

## For Different User Roles

### 👨‍💼 Super Admin

**Can Do Everything**

- View/Create/Edit/Delete all users
- Manage roles and permissions
- Access all reports
- Manage fees
- Take attendance
- Access all settings

**Menu Shows:**

- Dashboard
- All school management items
- Attendance
- Reports
- Fee Management
- Administration/Settings including user, role, and permission management

### 👨‍💼 Admin

**School Administrator - Limited to Administrative Tasks**

- View/Create/Edit/Delete users
- Manage roles and permissions
- Access school and system settings
- Cannot manage attendance or fees

**Menu Shows:**

- Dashboard
- Settings → Users, Roles, School/System Settings
- Cannot see: Attendance, Fee Management

**Cannot Access:**

- Taking attendance
- Fee collection
- Academic records

### 👨‍🏫 Teacher

**Limited to Teaching & Reporting**

- Take attendance ✅
- View attendance reports ✅
- View academic records ✅
- View reports ✅
- Cannot manage users, fees, or system settings

**Menu Shows:**

- Dashboard
- Attendance section with full options
- Reports section (for viewing only)
- Cannot see: Settings, Fee Management

**Cannot Access:**

- User management
- Role/Permission management
- Fee management
- System settings

### 👨‍🎓 Student

**Read-Only Access**

- View own attendance ✅
- View own academic records ✅
- View reports ✅
- Cannot create, edit, or delete anything

**Menu Shows:**

- Dashboard
- Reports section (for viewing only)
- Cannot see: Any administrative sections

**Cannot Access:**

- Attendance taking
- User management
- Fee management
- Settings

### 👨‍👩 Parent/Guardian

**Same as Student - Read-Only**

- Can view student's data (if parent-child relationship implemented)
- Read-only access to attendance and academic records

### 💰 Accountant

**Finance Specialist**

- Create/Edit/Delete fees ✅
- Collect fees ✅
- Generate financial reports ✅
- Cannot access attendance or academic records

**Menu Shows:**

- Dashboard
- Fee Management section with full options
- Reports section
- Cannot see: User Management, Attendance, Academic sections

**Cannot Access:**

- User management
- Attendance taking
- Academic record management
- Settings

## Checking Permissions in Code

### In Controller

```php
// Check single permission
if (auth()->user()->can('user.create')) {
    // Do something
}

// Check multiple permissions (OR)
if (auth()->user()->can('user.create') || auth()->user()->can('user.read')) {
    // Do something
}

// Check role
if (auth()->user()->hasRole('admin')) {
    // Do something
}

// Get all permissions
$permissions = auth()->user()->getAllPermissions();
```

### In Blade Template

```blade
<!-- Single permission -->
@can('user.create')
    <button>Create User</button>
@endcan

<!-- Multiple permissions (any)-->
@canany(['user.read', 'user.create'])
    <div>User management section</div>
@endcanany

<!-- Role check -->
@role('admin')
    <div>Admin only content</div>
@endrole

<!-- Negative check -->
@cannot('user.delete')
    <p>You cannot delete users</p>
@endcannot
```

### In Routes

```php
// Require permission
Route::middleware('can:user.create')->post('/users', [UserController::class, 'store']);

// Multiple permissions (OR logic)
Route::middleware('can:admin.access|super-admin.access')->group(function () {
    // Routes here
});
```

## Common Permission Checks

| Action          | Permission                  | Who Has It                           |
| --------------- | --------------------------- | ------------------------------------ |
| Create User     | `user.create`               | Admin, Super Admin                   |
| Edit User       | `user.update`               | Admin, Super Admin                   |
| Delete User     | `user.delete`               | Admin, Super Admin                   |
| View Attendance | `attendance.read`           | Teacher, Student, Admin, Super Admin |
| Take Attendance | `attendance.create`         | Teacher, Admin, Super Admin          |
| Collect Fee     | `fee.collect`               | Accountant, Admin, Super Admin       |
| View Reports    | `report.read`               | All authenticated users              |
| Manage Roles    | `role.create/update/delete` | Admin, Super Admin                   |
| Access Settings | `setting.read`              | Admin, Super Admin                   |

## Troubleshooting

### Permission Not Working?

**1. Check if permission exists:**

```bash
php artisan tinker
>>> \Spatie\Permission\Models\Permission::where('name', 'user.create')->exists();
# Should return true
```

**2. Check user's role:**

```bash
>>> auth()->user()->getRoleNames();
# Should list your role
```

**3. Check role has permission:**

```bash
>>> \Spatie\Permission\Models\Role::where('name', 'teacher')->first()->getPermissionNames();
# Should list permissions for that role
```

**4. Clear cache:**

```bash
php artisan cache:clear
php artisan permission:cache-reset
```

**5. Verify seeder ran:**

```bash
php artisan db:seed --class=PermissionSeeder
```

### Still Having Issues?

1. Check spelling of permission name
2. Verify role is assigned to user
3. Verify permission is assigned to role
4. Check that you logged in with correct user
5. Clear browser cache and refresh
6. Check route middleware syntax

## Adding New Permissions

### Option 1: Via Admin Panel

1. Go to Settings → Role & Permission Management
2. Click "Permissions"
3. Click "Add Permission"
4. Enter name like: `module.action`
5. Assign to roles

### Option 2: Via Code

Edit `database/seeders/PermissionSeeder.php`:

```php
$permissions = [
    'my_module' => [
        'my_module.create' => 'Create something',
        'my_module.read' => 'Read something',
        'my_module.update' => 'Update something',
        'my_module.delete' => 'Delete something',
    ],
];
```

Then run:

```bash
php artisan db:seed --class=PermissionSeeder
php artisan cache:clear
```

## Files to Know

| File                                             | Purpose                                   |
| ------------------------------------------------ | ----------------------------------------- |
| `routes/web.php`                                 | Permission middleware on routes           |
| `database/seeders/PermissionSeeder.php`          | Defines all permissions and roles         |
| `app/Models/User.php`                            | User model with HasRoles trait            |
| `resources/views/admin/users/*`                  | User management views (permission checks) |
| `resources/views/partials/app-sidebar.blade.php` | Sidebar menu (permission filtering)       |
| `resources/views/errors/403.blade.php`           | Access denied error page                  |
| `PERMISSIONS_IMPLEMENTATION.md`                  | Detailed implementation guide             |
| `PERMISSIONS_TESTING_GUIDE.md`                   | How to test permissions                   |

## Testing Your Permissions

### Quick Test

1. Login as **Admin**
    - ✅ Can access `/admin/users`
    - ✅ Can see "Users" in sidebar
    - ✅ Can see Create/Edit/Delete buttons

2. Login as **Teacher**
    - ❌ Cannot access `/admin/users` (403 error)
    - ❌ Cannot see "Users" in sidebar
    - ✅ Can access `/admin/attendance/take-attendance`

3. Login as **Accountant**
    - ❌ Cannot access `/admin/users`
    - ❌ Cannot see "Users" in sidebar
    - ✅ Can access `/admin/fee-management/collect-fees`

### Full Test

See `PERMISSIONS_TESTING_GUIDE.md` for comprehensive test cases.

## Key Concepts

### Permissions (What you can do)

- `user.create` - Create new users
- `attendance.read` - View attendance
- `fee.collect` - Receive payments

### Roles (Who you are)

- Admin - Has user management permissions
- Teacher - Has attendance permissions
- Accountant - Has fee management permissions

### Gates/Policies (Enforcement)

- Route middleware: `can:permission.name`
- Blade directives: `@can('permission.name')`
- Controller checks: `$this->authorize('action')`

## Useful Commands

```bash
# Clear permission cache
php artisan permission:cache-reset

# List all permissions
php artisan tinker
>>> \Spatie\Permission\Models\Permission::all();

# List all roles
>>> \Spatie\Permission\Models\Role::all();

# Check user permissions
>>> auth()->user()->getAllPermissions();

# Assign permission to role
>>> $role->givePermissionTo('permission.name');

# Remove permission from role
>>> $role->revokePermissionTo('permission.name');
```

## Important Reminders

✅ **Always check backend** - Never trust frontend permission checks  
✅ **Use middleware** - Preferred over controller checks  
✅ **Hide UI elements** - Use @can to hide buttons/links  
✅ **Clear cache** - After seeding or modifying permissions  
✅ **Test regularly** - Especially after adding new features  
✅ **Document new permissions** - Keep track of what you add  
✅ **Audit access** - Monitor who has what permissions  
✅ **Least privilege** - Only grant necessary permissions

## When to Use What

| Scenario             | Use                                       |
| -------------------- | ----------------------------------------- |
| Protect entire route | Route middleware: `middleware('can:...')` |
| Show/hide button     | Blade: `@can('...')`                      |
| Complex logic        | Controller method: `$this->authorize()`   |
| API endpoints        | Middleware + Controller both              |
| Sensitive operations | Always check backend + frontend           |

## Support & Reference

- **Implementation Details**: See `PERMISSIONS_IMPLEMENTATION.md`
- **Testing Guide**: See `PERMISSIONS_TESTING_GUIDE.md`
- **Implementation Summary**: See `PERMISSIONS_SUMMARY.md`
- **Spatie Package Docs**: https://spatie.be/docs/laravel-permission/
