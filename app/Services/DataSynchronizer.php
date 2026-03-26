<?php

namespace App\Services;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataSynchronizer
{
    /**
     * Sync all teachers to users table
     */
    public function syncTeachers($overwrite = false)
    {
        $synced = 0;
        $skipped = 0;
        $updated = 0;

        $teachers = Teacher::all();

        foreach ($teachers as $teacher) {
            try {
                // Check if user already exists with this email
                $user = User::where('email', $teacher->email)->first();

                if ($user && !$overwrite) {
                    // User exists, skip
                    $skipped++;
                    continue;
                }

                if ($user && $overwrite) {
                    // Update existing user
                    $user->update([
                        'name' => $teacher->first_name . ' ' . $teacher->last_name,
                        'password' => $teacher->password,
                        'syncable_type' => 'Teacher',
                        'syncable_id' => $teacher->id,
                        'synced' => true,
                    ]);
                    $updated++;
                } else {
                    // Create new user
                    User::create([
                        'name' => $teacher->first_name . ' ' . $teacher->last_name,
                        'email' => $teacher->email,
                        'password' => $teacher->password,
                        'syncable_type' => 'Teacher',
                        'syncable_id' => $teacher->id,
                        'synced' => true,
                    ]);
                    $synced++;
                }

                // Assign teacher role if not already assigned
                if ($user && !$user->hasRole('teacher')) {
                    $user->assignRole('teacher');
                } elseif (!$user && $user = User::where('email', $teacher->email)->first()) {
                    $user->assignRole('teacher');
                }
            } catch (\Exception $e) {
                Log::error('Error syncing teacher: ' . $e->getMessage(), [
                    'teacher_id' => $teacher->id,
                    'email' => $teacher->email,
                ]);
            }
        }

        return [
            'synced' => $synced,
            'skipped' => $skipped,
            'updated' => $updated,
            'total' => $teachers->count(),
            'type' => 'Teachers'
        ];
    }

    /**
     * Sync guardians from students table to users
     */
    public function syncGuardians($overwrite = false)
    {
        $synced = 0;
        $skipped = 0;
        $updated = 0;

        $students = Student::whereNotNull('parent_name')
            ->whereNotNull('parent_email')
            ->distinct('parent_email')
            ->get(['parent_name', 'parent_email']);

        foreach ($students as $student) {
            try {
                // Skip if email is not provided or empty
                if (empty($student->parent_email)) {
                    $skipped++;
                    continue;
                }

                // Check if user already exists with this email
                $user = User::where('email', $student->parent_email)->first();

                if ($user && !$overwrite) {
                    // User exists, skip
                    $skipped++;
                    continue;
                }

                if ($user && $overwrite) {
                    // Update existing user
                    $user->update([
                        'name' => $student->parent_name,
                        'syncable_type' => 'Guardian',
                        'synced' => true,
                    ]);
                    $updated++;
                } else {
                    // Create new user with a temporary password
                    User::create([
                        'name' => $student->parent_name,
                        'email' => $student->parent_email,
                        'password' => bcrypt('password'), // Temporary password
                        'syncable_type' => 'Guardian',
                        'synced' => true,
                    ]);
                    $synced++;
                }

                // Assign parent role if not already assigned
                $user = User::where('email', $student->parent_email)->first();
                if ($user && !$user->hasRole('parent')) {
                    $user->assignRole('parent');
                }
            } catch (\Exception $e) {
                Log::error('Error syncing guardian: ' . $e->getMessage(), [
                    'parent_email' => $student->parent_email,
                ]);
            }
        }

        return [
            'synced' => $synced,
            'skipped' => $skipped,
            'updated' => $updated,
            'total' => $students->count(),
            'type' => 'Guardians/Parents'
        ];
    }

    /**
     * Sync students to users table (optional - for student login)
     */
    public function syncStudents($overwrite = false)
    {
        $synced = 0;
        $skipped = 0;
        $updated = 0;

        $students = Student::all();

        foreach ($students as $student) {
            try {
                // Create email if not exists (using student_id@schoolms.com format)
                $email = $student->student_id . '@schoolms.com';

                // Check if user already exists
                $user = User::where('email', $email)->first();

                if ($user && !$overwrite) {
                    $skipped++;
                    continue;
                }

                if ($user && $overwrite) {
                    $user->update([
                        'name' => $student->first_name . ' ' . $student->last_name,
                        'syncable_type' => 'Student',
                        'syncable_id' => $student->id,
                        'synced' => true,
                    ]);
                    $updated++;
                } else {
                    User::create([
                        'name' => $student->first_name . ' ' . $student->last_name,
                        'email' => $email,
                        'password' => bcrypt('password'), // Temporary password
                        'syncable_type' => 'Student',
                        'syncable_id' => $student->id,
                        'synced' => true,
                    ]);
                    $synced++;
                }

                // Assign student role
                $user = User::where('email', $email)->first();
                if ($user && !$user->hasRole('student')) {
                    $user->assignRole('student');
                }
            } catch (\Exception $e) {
                Log::error('Error syncing student: ' . $e->getMessage(), [
                    'student_id' => $student->id,
                ]);
            }
        }

        return [
            'synced' => $synced,
            'skipped' => $skipped,
            'updated' => $updated,
            'total' => $students->count(),
            'type' => 'Students'
        ];
    }

    /**
     * Sync all data (teachers, guardians, students)
     */
    public function syncAll($overwrite = false)
    {
        return [
            'teachers' => $this->syncTeachers($overwrite),
            'guardians' => $this->syncGuardians($overwrite),
            'students' => $this->syncStudents($overwrite),
        ];
    }

    /**
     * Check for duplicate emails before sync
     */
    public function checkDuplicates()
    {
        $duplicateTeachers = Teacher::whereIn(
            'email',
            Teacher::selectRaw('email')
                ->groupBy('email')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('email')
        )->get();

        return [
            'duplicate_teacher_emails' => $duplicateTeachers->count(),
            'teachers' => $duplicateTeachers,
        ];
    }
}
