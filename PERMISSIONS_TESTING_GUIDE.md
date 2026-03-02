# Permission System Testing Guide

## Quick Test Checklist

Follow these steps to verify the permission system is working correctly across the application.

## Prerequisites

1. **Ensure migrations are run:**

    ```bash
    php artisan migrate
    ```

2. **Run permission seeder:**

    ```bash
    php artisan db:seed --class=PermissionSeeder
    ```

3. **Clear cache:**

    ```bash
    php artisan cache:clear
    php artisan permission:cache-reset
    ```

4. **Create test users** with different roles (via UI or seeder)

## Test Users Setup

Create these test users to verify role-based access:

| Email               | Password | Role        | Purpose               |
| ------------------- | -------- | ----------- | --------------------- |
| super@test.com      | password | super-admin | Full access test      |
| admin@test.com      | password | admin       | Admin-only features   |
| teacher@test.com    | password | teacher     | Attendance & academic |
| student@test.com    | password | student     | Read-only access      |
| accountant@test.com | password | accountant  | Fee management only   |

## Route-Level Permission Tests

### Test 1: User Management Access

**Super Admin / Admin should access:**

- ✅ GET `/admin/users` - View users list
- ✅ GET `/admin/users/create` - Create user form
- ✅ POST `/admin/users` - Create new user
- ✅ GET `/admin/users/{id}` - View user details
- ✅ GET `/admin/users/{id}/edit` - Edit user form
- ✅ PUT `/admin/users/{id}` - Update user
- ✅ DELETE `/admin/users/{id}` - Delete user
- ✅ POST `/admin/users/{id}/toggle-active` - Activate/deactivate

**Teacher / Student should NOT access:**

- ❌ Should get 403 Forbidden error
- ❌ Or be redirected to login/403 page

### Test 2: Attendance Routes

**Teacher should access:**

- ✅ GET `/admin/attendance/take-attendance` - Take attendance
- ✅ POST `/admin/attendance/take-attendance` - Submit attendance
- ✅ GET `/admin/attendance/class-report` - View class attendance
- ✅ GET `/admin/attendance/analytics` - View analytics

**Student should access:**

- ✅ GET `/admin/attendance/class-report` - View own attendance
- ❌ Should NOT be able to POST (take attendance)
- ❌ GET `/admin/attendance/take-attendance` - Should fail

**Accountant should NOT access:**

- ❌ Any attendance routes

### Test 3: Fee Management

**Accountant should access:**

- ✅ GET `/admin/fee-management/fees` - View fees
- ✅ GET `/admin/fee-management/collect-fees` - Collect fees page
- ✅ POST `/admin/fee-management/collect-fees` - Record payment

**Teacher/Student should NOT access:**

- ❌ Should get 403 error

**Admin should access:**

- ✅ Full fee management (has all permissions)

### Test 4: Role & Permission Management

**Super Admin / Admin should access:**

- ✅ GET `/admin/access-control/roles` - View roles
- ✅ GET `/admin/access-control/permissions` - View permissions
- ✅ Create/Edit/Delete roles and permissions

**Teacher / Student / Accountant should NOT access:**

- ❌ Should get 403 error

### Test 5: Settings Access

**Super Admin / Admin should access:**

- ✅ GET `/settings/school` - School settings
- ✅ GET `/settings/system` - System settings

**Teacher / Student should NOT access:**

- ❌ Should get 403 error

## View-Level Permission Tests

### Test 6: User Management UI

1. Login as **Admin**
2. Go to `/admin/users`
3. Verify:
    - ✅ "Add User" button visible
    - ✅ Edit buttons visible on user rows
    - ✅ Delete buttons visible on user rows

4. Login as **Teacher**
5. Navigate to `/admin/users` (should be blocked at route level)
6. If somehow you see the page, verify:
    - ❌ "Add User" button NOT visible
    - ❌ Edit/Delete buttons NOT visible

### Test 7: User Profile Page

1. Go to any user's detail page
2. Login as **Admin**:
    - ✅ "Edit User" button visible
    - ✅ "Delete User" button visible
    - ✅ "Deactivate User" button visible

3. Login as **Teacher**:
    - Route should be blocked (route-level check)

### Test 8: Sidebar Navigation

1. Login as **Super Admin / Admin**
    - ✅ See "Settings" section with:
        - Users submenu
        - Role & Permission Management submenu
        - User Role Assignment submenu
        - School Settings submenu
        - System Settings submenu
    - ✅ See "Reports" section

2. Login as **Teacher**
    - ✅ See "Reports" in sidebar
    - ❌ "Settings" section should NOT be visible
    - ❌ "Users" submenu should NOT be visible
    - ✅ Should see "Attendance" section

3. Login as **Accountant**
    - ❌ "Settings" section should NOT be visible
    - ❌ "Attendance" section should NOT be visible (not in their role)
    - ✅ Should see "Fee Management"

4. Login as **Student**
    - ❌ Should see LEAST number of menu items
    - ✅ Can view own attendance (if added to UI)
    - ❌ Cannot see admin features

## Blade Directive Tests

### Test 9: @can Directives

In any view with `@can` directives:

```blade
@can('user.create')
    <!-- This should only show if user has permission -->
@endcan
```

1. Login as user **WITH** permission
    - ✅ Content is visible
2. Login as user **WITHOUT** permission
    - ❌ Content is hidden

### Test 10: Multiple Permission Check

```blade
@canany(['user.read', 'role.read'])
    <!-- This shows if user has ANY of these -->
@endcanany
```

1. Login as **Admin** (has user.read)
    - ✅ Content visible
2. Login as **Accountant** (has neither)
    - ❌ Content hidden

## Error Page Test

### Test 11: 403 Access Denied Page

1. Try to manually access a protected route you don't have permission for
2. Navigate to `/admin/users` as **Accountant**
3. Should see the 403 error page with:
    - ✅ "403" heading
    - ✅ "Access Denied" message
    - ✅ Your assigned roles shown
    - ✅ "Back to Dashboard" button works

## Permission Checking in Code

### Test 12: Programmatic Permission Checks

Open a PHP file where authentication is available and test:

```php
// In controller or artisan command
if (auth()->user()->can('user.create')) {
    echo "User can create users";
}

if (auth()->user()->hasPermissionTo('attendance.read')) {
    echo "User can read attendance";
}

if (auth()->user()->hasRole('teacher')) {
    echo "User is a teacher";
}

// Check all permissions
$permissions = auth()->user()->getAllPermissions();
dd($permissions); // Should show all permissions for this user's roles
```

## Performance Tests

### Test 13: Permission Caching

1. First access should load permissions (slightly slower)
2. Subsequent accesses should use cache (faster)
3. Clear cache and verify it rebuilds:
    ```bash
    php artisan cache:clear
    php artisan permission:cache-reset
    ```

## Troubleshooting Tests

### Test 14: Debug Permission Issues

If permissions aren't working, run these checks:

**Check if permissions exist:**

```bash
php artisan tinker
>>> \Spatie\Permission\Models\Permission::all()->pluck('name');
```

**Check if user has role:**

```bash
>>> auth()->user()->getRoleNames(); // Should list assigned roles
```

**Check if role has permission:**

```bash
>>> \Spatie\Permission\Models\Role::where('name', 'teacher')->first()->getPermissionNames();
```

**Check user's all permissions:**

```bash
>>> auth()->user()->getAllPermissions()->pluck('name');
```

**Test permission directly:**

```bash
>>> auth()->user()->can('user.create'); // Should return true/false
```

## Cleanup

After testing, you can:

1. **Delete test users:**

    ```bash
    php artisan tinker
    >>> User::where('email', 'test@test.com')->delete();
    ```

2. **Reset to fresh data:**
    ```bash
    php artisan migrate:fresh --seed
    ```

## Test Results Checklist

Copy and fill this out after testing:

```
✅ Route-level permissions working: [ YES / NO ]
✅ View-level @can directives working: [ YES / NO ]
✅ Sidebar menu filtering working: [ YES / NO ]
✅ Action buttons hidden correctly: [ YES / NO ]
✅ 403 error page displays: [ YES / NO ]
✅ Permission caching working: [ YES / NO ]
✅ Super admin has all access: [ YES / NO ]
✅ Admin has limited access: [ YES / NO ]
✅ Teacher can take attendance: [ YES / NO ]
✅ Student has read-only access: [ YES / NO ]
✅ Accountant controls fees only: [ YES / NO ]

Overall Status: [ PASS / FAIL ]
Date Tested: [ __________ ]
Tester: [ __________ ]
Notes: [ __________ ]
```

## Frequently Tested Routes

| Route                                | Admin | Teacher | Student | Method | Expected                        |
| ------------------------------------ | ----- | ------- | ------- | ------ | ------------------------------- |
| `/admin/users`                       | ✅    | ❌      | ❌      | GET    | 403 for non-admin               |
| `/admin/attendance/take-attendance`  | ✅    | ✅      | ❌      | GET    | 403 for non-teacher             |
| `/admin/fee-management/collect-fees` | ✅    | ❌      | ❌      | GET    | 403 for non-accountant          |
| `/admin/access-control/roles`        | ✅    | ❌      | ❌      | GET    | 403 for non-admin               |
| `/reports/attendance`                | ✅    | ✅      | ✅      | GET    | All can view if have permission |
| `/admin/students`                    | ✅    | ✅\*    | ❌      | GET    | Teacher/Admin only              |

\*With appropriate academic permissions
