# Role-Based Results Viewer Guide

## Overview

The Results Viewer is a unified, role-based interface that allows different user types to view academic results:

- **Parents**: View only their own children's results
- **Students**: View their own results only
- **Teachers**: View students' results for classes they teach
- **Admins**: View all students' results

## Routes

All results viewer routes are under the `/results-viewer` prefix:

```
GET  /results-viewer              - View results page (results-viewer.view)
GET  /results-viewer/students     - Get students for filter dropdown - AJAX (results-viewer.get-students)
POST /results-viewer/export       - Export results functionality (results-viewer.export)
```

## Features

### 1. Role-Based Access Control

#### Parents

- Can only see results for their own children
- Accesses students via `parent_email` matching their account email
- Supports multiple children on one page

#### Students

- Can only access their own results
- Looks up student record by `student_id` matching user ID

#### Admins/Super-Admins

- Can view all students' results
- Full access without restrictions

#### Teachers

- Can view results (requires `academic.read` permission)

### 2. Result Organization

Results are organized hierarchically:

```
Student
├── Subject 1
│   ├── Assessment 1: 85
│   ├── Assessment 2: 90
│   └── Average: 87.5
├── Subject 2
│   ├── Assessment 1: 78
│   ├── Assessment 2: 82
│   └── Average: 80
└── Total Subjects: 2
```

### 3. Filtering

Users can filter results by:

- **Academic Year**: Filter results by year
- **Academic Period**: Filter results by period (term, semester, etc.)
- **Student** (Admins only): Filter to specific student

### 4. Data Calculation

- **Subject Total**: Sum of all assessment scores for that subject
- **Subject Average**: Subject total divided by number of assessments
- **Result Count**: Displayed in card summary

### 5. Export Functionality

Currently shows placeholder message. Ready for integration with:

- dompdf (PDF export)
- maatwebsite/excel (Excel export)

## Models and Relationships

### Student Model

```php
// Get parent/guardian user account
$student->parentUser()  // belongsTo User

// Get all results for student
$student->results()     // hasMany Result
```

### Result Model

```php
// Related models
$result->student()           // belongsTo Student
$result->assessment()        // belongsTo Assessment
$result->academicYear()      // belongsTo AcademicYear
$result->academicPeriod()    // belongsTo AcademicPeriod
```

### Assessment Model

```php
// Subject for this assessment
$assessment->subject()  // belongsTo Subject
$assessment->class()    // belongsTo ClassModel
```

## Controller Overview

**File**: `app/Http/Controllers/ResultsViewerController.php`

### Methods

#### `viewResults(Request $request)`

Main method that displays results with role-based filtering.

**Parameters**:

- `academic_year_id` (query): Filter by academic year
- `academic_period_id` (query): Filter by period
- `student_id` (query): Filter by student (admins only)

**Returns**: View with organized results data

**Logic**:

```php
if (parent role) {
    $students = Student::where('parent_email', user.email)
} else if (admin role) {
    $students = Student::all()
} else if (student role) {
    $students = [current student]
}

// Organize by student -> subject -> assessment
// Calculate averages
// Return view
```

#### `getStudents(Request $request)`

AJAX endpoint for populating student filter dropdown.

**Returns**: JSON array with student options

```json
[
    { "id": 1, "name": "John Doe" },
    { "id": 2, "name": "Jane Doe" }
]
```

#### `exportResults(Request $request)`

Placeholder for export functionality.

**Parameters**:

- `format`: 'pdf' or 'excel' (not implemented yet)

**Returns**: Redirect with success message

## View Template

**File**: `resources/views/results/view.blade.php`

### Structure

- Header with page title
- Alert messages (success/error)
- Filter form (academic year, period, student)
- Summary cards (student count, subject count, assessment count)
- Student result cards organized by:
    - Student name with ID
    - Status badge
    - Academic period info
    - Subjects with:
        - Assessment scores
        - Subject subtotal
        - Subject average

### Empty States

- No students: Shows message based on user role
- No results: Shows appropriate empty state alert

## Sidebar Integration

The Results Viewer appears in the sidebar under "Results Management":

- **View My Results**: Links to `/results-viewer` (visible to all authenticated users)
- **View All Results**: Links to `/results/index` (admin only)
- Other result-related items (assessments, uploads)

## Usage Examples

### For Parents

1. Login as parent
2. Click "Results Management" → "View My Results"
3. See all children's results
4. Filter by academic year/period
5. View individual children's performance

### For Students

1. Login as student
2. Click "Results Management" → "View My Results"
3. See own results only
4. Filter by year/period
5. Track personal performance

### For Admins

1. Login as admin
2. Click "Results Management" → "View My Results"
3. See all students' results
4. Filter by year, period, or specific student
5. Monitor complete school performance

## Database Requirements

Ensure these tables and relationships exist:

- `students` table with `parent_email` and `student_id` columns
- `results` table with:
    - `student_id` (foreign key to students)
    - `assessment_id` (foreign key to assessments)
    - `score` (decimal)
    - `academic_year_id`
    - `academic_period_id`
- `assessments` table with `subject_id` and `class_id`
- `subjects` table
- `academic_years` table
- `academic_periods` table

## Troubleshooting

### Issue: "You do not have permission to view results"

- **Cause**: User doesn't have appropriate role
- **Solution**: Assign parent, student, admin, or teacher role to user

### Issue: Parents don't see their children's results

- **Cause**: `parent_email` in students table doesn't match user email
- **Solution**: Update students table with correct parent emails

### Issue: No results showing

- **Cause**: No result records exist for the student/period
- **Solution**: Ensure results have been uploaded via results management

### Issue: Sidebar link not showing

- **Cause**: User doesn't have required authentication
- **Solution**: Ensure user is logged in (auth middleware is applied)

## Future Enhancements

1. **PDF/Excel Export**: Integrate dompdf or maatwebsite/excel
2. **Print Results**: Add print-friendly layout
3. **Result Comparison**: Compare multiple students' results
4. **Performance Tracking**: Show trends over time
5. **Notifications**: Notify parents of new results
6. **Comments**: Add teacher comments to results
7. **Progress Charts**: Visualize performance with charts
8. **Bulk Download**: Download all results as zip

## Permission Matrix

| Action                  | Parent | Student | Teacher | Admin | SuperAdmin |
| ----------------------- | ------ | ------- | ------- | ----- | ---------- |
| View Own Results        | ✓      | ✓       | ✓       | ✓     | ✓          |
| View Children's Results | ✓      | -       | -       | -     | -          |
| View Class Results      | -      | -       | ✓       | -     | -          |
| View All Results        | -      | -       | -       | ✓     | ✓          |
| Export Results          | -      | -       | -       | ✓     | ✓          |
| Create/Edit Results     | -      | -       | -       | ✓     | ✓          |

## File Locations

- **Controller**: `app/Http/Controllers/ResultsViewerController.php`
- **View**: `resources/views/results/view.blade.php`
- **Routes**: `routes/web.php` (lines 264-269)
- **Sidebar**: `resources/views/partials/app-sidebar.blade.php` (Results Management section)
