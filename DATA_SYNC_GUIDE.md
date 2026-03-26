# Data Synchronization Guide

## Overview

The Data Synchronization feature allows you to sync user data from multiple tables (teachers, students) into the main `users` table for centralized authentication and access control.

## Features

✅ **Teachers Sync** - Migrate teachers from `teachers` table to `users` table with their email and password
✅ **Guardians/Parents Sync** - Extract parent/guardian info from `students` table and create user accounts
✅ **Students Sync** (Optional) - Create student user accounts for login access
✅ **Duplicate Prevention** - Automatically checks and prevents duplicate email accounts
✅ **Overwrite Option** - Update existing users if needed
✅ **Role Assignment** - Automatically assigns appropriate roles (teacher, parent, student) after sync
✅ **CLI Command** - Run sync from command line or web interface

## Database Schema

### Users Table (Updated)

```sql
users
├── id (primary key)
├── name (string)
├── email (string, unique)
├── password (string)
├── email_verified_at (timestamp, nullable)
├── remember_token
├── syncable_type (string, nullable) -- 'Teacher', 'Guardian', 'Student'
├── syncable_id (bigint, nullable) -- ID from source table
├── synced (boolean) -- Whether user was synced from another table
├── created_at
└── updated_at
```

The `syncable_type` and `syncable_id` fields track the origin of synced users.

## How to Use

### Method 1: Web Interface (Admin Panel)

1. Go to **Admin → Data Synchronization** (`/admin/data-sync`)
2. You'll see cards for each sync type:
    - **Sync Teachers** - Syncs teachers table to users
    - **Sync Guardians/Parents** - Extracts parent info from students table
    - **Sync Students** - Creates student login accounts
    - **Sync All** - Does all three at once

3. Optional: Check "Overwrite existing users" if you want to update existing accounts
4. Click the sync button
5. View results showing how many were created, updated, or skipped

### Method 2: Command Line

```bash
# Sync all data (teachers, guardians, students)
php artisan sync:data all

# Sync specific source
php artisan sync:data teachers
php artisan sync:data guardians
php artisan sync:data students

# Overwrite existing users
php artisan sync:data all --overwrite

# Check for duplicate emails before syncing
php artisan sync:data all --check-duplicates

# Combine options
php artisan sync:data teachers --overwrite --check-duplicates
```

## Sync Behavior

### Teachers Sync

- **Source Data**: `teachers` table
- **Matched On**: Email address
- **Fields Synced**:
    - Name: `first_name` + `last_name`
    - Email: `email`
    - Password: `password` (stored as-is, no hashing)
- **Role Assigned**: `teacher`
- **Email Format**: Uses existing teacher email

### Guardians/Parents Sync

- **Source Data**: `students` table (`parent_name`, `parent_email`)
- **Matched On**: Email address
- **Fields Synced**:
    - Name: `parent_name`
    - Email: `parent_email`
    - Password: Auto-generated temporary password (`password`)
- **Role Assigned**: `parent`
- **Note**: Only syncs students with non-null `parent_email`

### Students Sync (Optional)

- **Source Data**: `students` table
- **Matched On**: Generated email `{student_id}@schoolms.com`
- **Fields Synced**:
    - Name: `first_name` + `last_name`
    - Email: Auto-generated from `student_id`
    - Password: Temporary password (`password`)
- **Role Assigned**: `student`
- **Default Password**: `password` (users should change on first login)

## Duplicate Prevention

### Automatic Duplicate Handling

The sync process automatically:

1. Checks if a user with the same email already exists
2. **If exists and `--overwrite` NOT set**: Skips the record (counts as "skipped")
3. **If exists and `--overwrite` set**: Updates the existing user record
4. **If new**: Creates a new user account

### Manual Duplicate Check

```bash
php artisan sync:data all --check-duplicates
```

This will show any duplicate emails in the database before syncing.

## Sync Results

After each sync, you'll see:

- **Created**: New user accounts created
- **Updated**: Existing accounts updated (only with `--overwrite`)
- **Skipped**: Records not synced (usually because user already exists)
- **Total**: Total records processed

Example output:

```
📊 Teachers Synchronization Results:
┌────────────────┬───────┬────────┐
│ Metric         │ Count │ Status │
├────────────────┼───────┼────────┤
│ Total Records  │   45  │        │
│ Created        │   42  │ ✅     │
│ Updated        │    0  │        │
│ Skipped        │    3  │ ⏭️     │
└────────────────┴───────┴────────┘
```

## Important Notes

⚠️ **Password Handling**

- **Teachers**: Uses existing password from `teachers` table
- **Guardians/Students**: Creates temporary password `password`
- After syncing, users should reset their passwords on first login

⚠️ **Email Uniqueness**

- Emails must be unique in the system
- If a teacher and guardian have the same email, one will be skipped
- Use `--check-duplicates` to identify and resolve conflicts first

⚠️ **Role Assignment**

- Role-based access control requires Spatie Permission to be installed
- Each synced user is automatically assigned appropriate role
- Existing roles are preserved unless overwritten

⚠️ **Data Integrity**

- Synced data is marked with `synced = true` and `syncable_type/syncable_id`
- Original records in source tables are NOT deleted
- You can restore by syncing again with `--overwrite`

## Troubleshooting

### "No permission named X" Error

**Cause**: The required role doesn't exist
**Solution**: Run the PermissionSeeder first

```bash
php artisan db:seed --class=PermissionSeeder
```

### Duplicate Email Conflicts

**Cause**: Same email exists in multiple source tables
**Solution**:

```bash
php artisan sync:data all --check-duplicates
# Then manually resolve conflicts in the database
```

### Users Not Getting Roles

**Cause**: Spatie Permission not configured
**Solution**: Ensure PermissionSeeder has been run and roles exist

## Example Scenarios

### Scenario 1: Migrate All Teachers

```bash
# First, check for issues
php artisan sync:data teachers --check-duplicates

# Then sync
php artisan sync:data teachers

# Result: All teachers now have user accounts and "teacher" role
```

### Scenario 2: Add Guardian Access

```bash
# Sync parent information from students table
php artisan sync:data guardians

# Result: Parents/guardians can now login to view student info
```

### Scenario 3: Update All User Data

```bash
# Sync all and update existing records
php artisan sync:data all --overwrite

# Result: All user data refreshed from source tables
```

## SQL Queries

### Check Synced Users

```sql
-- Find all synced users
SELECT * FROM users WHERE synced = true;

-- Find users by type
SELECT * FROM users WHERE syncable_type = 'Teacher';
SELECT * FROM users WHERE syncable_type = 'Guardian';
SELECT * FROM users WHERE syncable_type = 'Student';

-- Find non-synced (manual) users
SELECT * FROM users WHERE synced = false OR synced IS NULL;
```

### Reset Sync Status

```sql
-- Mark all sync data as not synced (for re-syncing)
UPDATE users SET synced = false WHERE synced = true;
```

## What's Next?

After syncing:

1. **Test Login**: Try logging in with synced accounts
2. **Reset Passwords**: Ask users to reset their default password
3. **Verify Roles**: Check that users have correct permissions
4. **Setup 2FA** (Optional): Enable two-factor authentication for security

## Support

For issues or questions:

1. Check the logs: `storage/logs/laravel.log`
2. Verify synced users: Check `users` table for `synced = true` records
3. Test individual syncs before syncing all data
4. Always back up database before large sync operations
