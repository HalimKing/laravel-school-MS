<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\ClassModel;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ResultsController extends Controller
{
    /**
     * Display all results with filters - pivoted by student
     */
    public function index(Request $request)
    {
        $resultsQuery = Result::with(['student', 'assessment', 'academicYear', 'academicPeriod']);

        // Apply filters
        if ($request->filled('academic_year_id')) {
            $resultsQuery->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('academic_period_id')) {
            $resultsQuery->where('academic_period_id', $request->academic_period_id);
        }

        if ($request->filled('subject_id')) {
            $resultsQuery->whereHas('assessment', function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }

        if ($request->filled('class_id')) {
            $resultsQuery->whereHas('assessment', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        // Get all results with filters
        $allResults = $resultsQuery->get();

        // Pivot data by student
        $studentResults = [];
        $assessmentNames = [];

        foreach ($allResults as $result) {
            $studentId = $result->student->id;
            $assessmentName = $result->assessment->name;

            if (!isset($studentResults[$studentId])) {
                $studentResults[$studentId] = [
                    'student' => $result->student,
                    'assessments' => [],
                    'scores' => [],
                    'resultIds' => [],
                    'academicYear' => $result->academicYear,
                    'academicPeriod' => $result->academicPeriod,
                ];
            }

            $studentResults[$studentId]['assessments'][$assessmentName] = $result->assessment;
            $studentResults[$studentId]['scores'][$assessmentName] = $result->score;
            $studentResults[$studentId]['resultIds'][$assessmentName] = $result->id;

            if (!in_array($assessmentName, $assessmentNames)) {
                $assessmentNames[] = $assessmentName;
            }
        }

        // Calculate final scores (sum of all assessment scores)
        foreach ($studentResults as &$data) {
            $data['finalScore'] = array_sum($data['scores']);
        }
        unset($data);

        // Sort students
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'score') {
            usort($studentResults, function ($a, $b) use ($sortOrder) {
                $comparison = $a['finalScore'] <=> $b['finalScore'];
                return $sortOrder === 'asc' ? $comparison : -$comparison;
            });
        }

        // Convert to collection for pagination
        $page = request('page', 1);
        $perPage = 50;
        $paginatedResults = collect($studentResults)
            ->slice(($page - 1) * $perPage, $perPage)
            ->values();

        // Create a paginator instance
        $results = new \Illuminate\Pagination\Paginator(
            $paginatedResults,
            $perPage,
            $page,
            [
                'path' => route('results.index'),
                'query' => $request->query(),
            ]
        );

        // Get filter options
        $academicYears = AcademicYear::where('status', 'active')->get();
        $academicPeriods = AcademicPeriod::all();
        $subjects = Subject::all();
        $classes = ClassModel::all();

        return view('results.index', compact(
            'results',
            'assessmentNames',
            'academicYears',
            'academicPeriods',
            'subjects',
            'classes'
        ));
    }

    /**
     * Show single result upload form
     */
    public function singleUpload()
    {
        $academicYears = AcademicYear::where('status', 'active')->get();
        $academicPeriods = AcademicPeriod::all();
        $subjects = Subject::all();
        $classes = ClassModel::all();

        return view('results.single-upload', compact('academicYears', 'academicPeriods', 'subjects', 'classes'));
    }

    /**
     * Get students for a specific class and academic year
     */
    public function getStudentsByClass($academicYearId, $classId)
    {
        $students = Student::whereHas('levels', function ($query) use ($classId, $academicYearId) {
            $query->where('level_data.class_id', $classId)
                ->where('level_data.academic_year_id', $academicYearId);
        })
            ->orderBy('students.first_name')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'registration_number' => $student->student_id,
                ];
            });

        return response()->json($students);
    }

    /**
     * Get assessments for a specific subject and class
     */
    public function getAssessmentsBySubject($subjectId, $classId)
    {
        $assessments = Assessment::where('subject_id', $subjectId)
            ->where('class_id', $classId)
            ->orderBy('name')
            ->get()
            ->map(function ($assessment) {
                return [
                    'id' => $assessment->id,
                    'name' => $assessment->name,
                    'percentage' => $assessment->percentage,
                ];
            });

        return response()->json($assessments);
    }

    /**
     * Check for duplicate results
     */
    public function checkDuplicates(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'assessment_ids' => 'required|array|min:1',
            'assessment_ids.*' => 'exists:assessments,id',
        ]);

        $duplicates = [];

        foreach ($validated['assessment_ids'] as $assessmentId) {
            $existingResult = Result::where('student_id', $validated['student_id'])
                ->where('assessment_id', $assessmentId)
                ->where('academic_year_id', $validated['academic_year_id'])
                ->where('academic_period_id', $validated['academic_period_id'])
                ->with('assessment')
                ->first();

            if ($existingResult) {
                $duplicates[] = [
                    'assessment_id' => $assessmentId,
                    'assessment_name' => $existingResult->assessment->name,
                    'existing_score' => $existingResult->score,
                ];
            }
        }

        return response()->json([
            'has_duplicates' => count($duplicates) > 0,
            'duplicates' => $duplicates,
        ]);
    }

    /**
     * Store single result
     */
    public function storeSingleResult(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'scores' => 'required|array|min:1',
            'scores.*' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $successCount = 0;
            $updateCount = 0;

            // Process each assessment score
            foreach ($validated['scores'] as $assessmentId => $score) {
                // Skip if score is empty
                if ($score === null || $score === '') {
                    continue;
                }

                // Check if assessment exists
                $assessment = Assessment::find($assessmentId);
                if (!$assessment) {
                    continue;
                }

                // Use updateOrCreate to either update existing or create new
                $result = Result::updateOrCreate(
                    [
                        'student_id' => $validated['student_id'],
                        'assessment_id' => $assessmentId,
                        'academic_year_id' => $validated['academic_year_id'],
                        'academic_period_id' => $validated['academic_period_id'],
                    ],
                    [
                        'score' => $score,
                    ]
                );

                // Check if it was updated or created
                if ($result->wasRecentlyCreated) {
                    $successCount++;
                } else {
                    $updateCount++;
                }
            }

            // Build response message
            $message = '';

            if ($successCount > 0) {
                $message = "$successCount new assessment score(s) recorded successfully!";
            }

            if ($updateCount > 0) {
                if ($message) {
                    $message .= " And $updateCount assessment score(s) were updated.";
                } else {
                    $message = "$updateCount assessment score(s) were updated successfully!";
                }
            }

            if ($successCount === 0 && $updateCount === 0) {
                return redirect()->back()->withInput()->with('error', 'No scores were entered. Please enter at least one score.');
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error("Result Store Error: " . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error saving results: ' . $e->getMessage());
        }
    }

    /**
     * Show bulk upload form
     */
    public function bulkUpload()
    {
        $academicYears = AcademicYear::where('status', 'active')->get();
        $academicPeriods = AcademicPeriod::all();
        $subjects = Subject::all();
        $classes = ClassModel::all();

        return view('results.bulk-upload', compact('academicYears', 'academicPeriods', 'subjects', 'classes'));
    }

    /**
     * Process bulk upload via CSV/Excel
     */
    public function processBulkUpload(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
            'academic_year_id' => 'required|exists:academic_years,id',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_models,id',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('file');
            $parsedData = $this->parseFile($file);

            if (empty($parsedData['data'])) {
                return redirect()->back()->with('error', 'The file appears to be empty. Please check the file format.');
            }

            $headers = $parsedData['headers']; // Assessment names from header
            $rows = $parsedData['data'];

            // Load assessments for this subject and class
            $assessments = Assessment::where('subject_id', $validated['subject_id'])
                ->where('class_id', $validated['class_id'])
                ->get()
                ->keyBy('name');

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2; // +2 for header and 0-index
                $studentId = $row['registration_number'] ?? null;

                if (empty($studentId)) {
                    $errorCount++;
                    $errors[] = "Line {$lineNumber}: Student ID is missing.";
                    continue;
                }

                // Find student with the student_id (check through LevelData for class)
                $student = Student::whereHas('levels', function ($query) use ($validated) {
                    $query->where('level_data.class_id', $validated['class_id']);
                })
                    ->where('students.student_id', trim($studentId))
                    ->first();

                if (!$student) {
                    $errorCount++;
                    $errors[] = "Line {$lineNumber}: Student '{$studentId}' not found or not in the selected class.";
                    continue;
                }

                // Process each assessment column
                $rowSuccessful = false;
                foreach ($headers as $assessmentName) {
                    $score = $row[$assessmentName] ?? null;

                    // Skip empty scores
                    if (empty($score) || $score === '') {
                        continue;
                    }

                    // Validate score
                    if (!is_numeric($score) || $score < 0 || $score > 100) {
                        $errorCount++;
                        $errors[] = "Line {$lineNumber}: Invalid score '{$score}' for assessment '{$assessmentName}'. Score must be between 0-100.";
                        continue;
                    }

                    // Find assessment
                    if (!isset($assessments[$assessmentName])) {
                        $errorCount++;
                        $errors[] = "Line {$lineNumber}: Assessment '{$assessmentName}' not found for this subject and class.";
                        continue;
                    }

                    $assessment = $assessments[$assessmentName];

                    // Check if result already exists
                    $existingResult = Result::where('student_id', $student->id)
                        ->where('assessment_id', $assessment->id)
                        ->where('academic_year_id', $validated['academic_year_id'])
                        ->where('academic_period_id', $validated['academic_period_id'])
                        ->first();

                    if ($existingResult) {
                        // Update existing
                        $existingResult->update(['score' => $score]);
                    } else {
                        // Create new
                        Result::create([
                            'student_id' => $student->id,
                            'assessment_id' => $assessment->id,
                            'score' => $score,
                            'academic_year_id' => $validated['academic_year_id'],
                            'academic_period_id' => $validated['academic_period_id'],
                        ]);
                    }

                    $successCount++;
                    $rowSuccessful = true;
                }
            }

            DB::commit();

            if ($errorCount > 0) {
                session()->flash('partial_success', "Imported {$successCount} scores with {$errorCount} errors.");
                session()->flash('errors', $errors);
            } else {
                session()->flash('success', "Successfully imported {$successCount} scores!");
            }

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Bulk Upload Error: " . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bulk upload error: ' . $e->getMessage() . '. Please check the file format and try again.');
        }
    }

    /**
     * Parse CSV/Excel file with assessment columns
     */
    private function parseFile($file)
    {
        $rows = [];
        $headers = [];
        $filePath = $file->path();
        $extension = $file->getClientOriginalExtension();

        try {
            if (in_array($extension, ['xlsx', 'xls'])) {
                // For Excel files, try to use PhpOffice if available
                $factoryClass = 'PhpOffice\PhpSpreadsheet\IOFactory';
                if (class_exists($factoryClass)) {
                    $spreadsheet = call_user_func([$factoryClass, 'load'], $filePath);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $highestRow = $worksheet->getHighestRow();
                    $highestColumn = $worksheet->getHighestColumn();

                    // Extract headers from first row (skip first 2 columns: Registration Number, Student Name)
                    $headerArray = $worksheet->rangeToArray("A1:" . $highestColumn . "1")[0];
                    $headers = array_map(fn($h) => trim($h ?? ''), array_slice($headerArray, 2));
                    // Remove trailing empty headers only
                    $headers = array_values(array_filter($headers, fn($h, $k) => $h !== '' || isset($headers[$k + 1]) && $headers[$k + 1] !== '', ARRAY_FILTER_USE_BOTH));

                    // Extract data rows
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $regNum = $worksheet->getCell('A' . $row)->getValue();
                        if (empty($regNum)) {
                            continue;
                        }

                        $rowData = ['registration_number' => $regNum];

                        // Map assessment scores to headers (start from column C, skip A and B)
                        for ($col = 3; $col <= count($headers) + 2; $col++) {
                            $cellValue = $worksheet->getCell(chr(64 + $col) . $row)->getValue();
                            $headerIndex = $col - 3;
                            if (isset($headers[$headerIndex]) && $headers[$headerIndex] !== '') {
                                $rowData[$headers[$headerIndex]] = $cellValue;
                            }
                        }

                        $rows[] = $rowData;
                    }
                } else {
                    throw new \Exception('Excel file support requires phpoffice/phpspreadsheet. Please use CSV format or install the package.');
                }
            } else {
                // Handle CSV and TXT files
                if (!file_exists($filePath)) {
                    throw new \Exception('File does not exist: ' . $filePath);
                }

                $handle = fopen($filePath, 'r');
                if (!$handle) {
                    throw new \Exception('Unable to open file for reading');
                }

                // Read header row
                if (!($headerRow = fgetcsv($handle))) {
                    fclose($handle);
                    throw new \Exception('File appears to be empty or invalid format');
                }

                // Extract headers (skip first 2 columns: Registration Number, Student Name)
                $headers = array_map(fn($h) => trim($h ?? ''), array_slice($headerRow, 2));
                // Remove trailing empty headers
                $headers = array_values(array_filter($headers, fn($h, $k) => $h !== '' || isset($headers[$k + 1]) && $headers[$k + 1] !== '', ARRAY_FILTER_USE_BOTH));

                // Read data rows
                while (($row = fgetcsv($handle)) !== false) {
                    if (empty($row[0])) { // Skip if registration number is empty
                        continue;
                    }

                    $rowData = ['registration_number' => trim($row[0])];

                    // Map assessment scores to headers (start from column 3rd index, skip first 2)
                    for ($i = 0; $i < count($headers); $i++) {
                        if ($headers[$i] !== '') {
                            $rowData[$headers[$i]] = isset($row[$i + 2]) ? $row[$i + 2] : null;
                        }
                    }

                    if (!empty($rowData['registration_number'])) {
                        $rows[] = $rowData;
                    }
                }

                fclose($handle);
            }

            if (empty($headers)) {
                throw new \Exception('No assessment columns found in the file. Please check the file format.');
            }

            return [
                'headers' => $headers,
                'data' => $rows
            ];
        } catch (\Exception $e) {
            throw new \Exception('File parsing error: ' . $e->getMessage());
        }
    }

    /**
     * Download dynamic template based on subject and class assessments
     */
    public function downloadTemplate($subjectId, $classId)
    {
        // Get assessments for this subject and class
        $assessments = Assessment::where('subject_id', $subjectId)
            ->where('class_id', $classId)
            ->orderBy('name')
            ->get();

        if ($assessments->isEmpty()) {
            return redirect()->back()->with('error', 'No assessments found for the selected subject and class.');
        }

        // Get active academic year
        $activeYear = AcademicYear::where('status', 'active')->first();

        // Get students for this class in the active academic year
        $classModel = ClassModel::find($classId);
        $students = Student::whereHas('levels', function ($query) use ($classModel, $activeYear) {
            $query->where('level_data.class_id', $classModel->id);
            if ($activeYear) {
                $query->where('level_data.academic_year_id', $activeYear->id);
            }
        })->get();

        // Build CSV header with Student ID, Name + Assessment names
        $headers = ['Registration Number', 'Student Name'];
        foreach ($assessments as $assessment) {
            $headers[] = $assessment->name;
        }

        // Build CSV content with student data
        $csvContent = implode(',', array_map(function ($h) {
            // Escape quotes in headers
            return '"' . str_replace('"', '""', $h) . '"';
        }, $headers)) . "\n";

        // Add student rows
        foreach ($students as $student) {
            $row = [
                $student->student_id,
                $student->first_name . ' ' . $student->last_name
            ];
            foreach ($assessments as $assessment) {
                $row[] = ''; // Empty score field for user to fill
            }
            $csvContent .= implode(',', array_map(function ($field) {
                // Escape quotes in data
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row)) . "\n";
        }

        // Return as downloadable CSV
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="results_template_' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Delete a result
     */
    public function destroy($id)
    {
        try {
            $result = Result::findOrFail($id);
            $result->delete();

            return redirect()->back()->with('success', 'Result deleted successfully!');
        } catch (\Exception $e) {
            Log::error("Result Delete Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting result: ' . $e->getMessage());
        }
    }

    /**
     * Delete all results for a student in a specific academic year and period
     */
    public function deleteStudentResults(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|numeric|exists:students,id',
                'academic_year_id' => 'required|numeric|exists:academic_years,id',
                'academic_period_id' => 'required|numeric|exists:academic_periods,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning("Delete Student Results Validation Error: " . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . json_encode($e->errors()),
            ], 422);
        }

        try {
            $deletedCount = Result::where('student_id', $validated['student_id'])
                ->where('academic_year_id', $validated['academic_year_id'])
                ->where('academic_period_id', $validated['academic_period_id'])
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "$deletedCount result(s) deleted successfully!",
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            Log::error("Delete Student Results Error: " . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting results: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export results to CSV
     */
    public function export(Request $request)
    {
        try {
            $resultsQuery = Result::with(['student', 'assessment', 'academicYear', 'academicPeriod']);

            // Apply filters
            if ($request->filled('academic_year_id')) {
                $resultsQuery->where('academic_year_id', $request->academic_year_id);
            }

            if ($request->filled('academic_period_id')) {
                $resultsQuery->where('academic_period_id', $request->academic_period_id);
            }

            if ($request->filled('subject_id')) {
                $resultsQuery->whereHas('assessment', function ($q) use ($request) {
                    $q->where('subject_id', $request->subject_id);
                });
            }

            if ($request->filled('class_id')) {
                $resultsQuery->whereHas('assessment', function ($q) use ($request) {
                    $q->where('class_id', $request->class_id);
                });
            }

            $allResults = $resultsQuery->get();

            // Pivot data by student
            $studentResults = [];
            $assessmentNames = [];

            foreach ($allResults as $result) {
                $studentId = $result->student->id;
                $assessmentName = $result->assessment->name;

                if (!isset($studentResults[$studentId])) {
                    $studentResults[$studentId] = [
                        'student' => $result->student,
                        'assessments' => [],
                        'scores' => [],
                        'academicYear' => $result->academicYear,
                        'academicPeriod' => $result->academicPeriod,
                    ];
                }

                $studentResults[$studentId]['assessments'][$assessmentName] = $result->assessment;
                $studentResults[$studentId]['scores'][$assessmentName] = $result->score;

                if (!in_array($assessmentName, $assessmentNames)) {
                    $assessmentNames[] = $assessmentName;
                }
            }

            // Calculate final scores
            foreach ($studentResults as &$data) {
                $data['finalScore'] = array_sum($data['scores']);
            }
            unset($data);

            // Generate CSV
            $filename = 'results_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename"
            );

            // Create callback function for CSV output
            $callback = function () use ($studentResults, $assessmentNames) {
                $file = fopen('php://output', 'w');

                // Write header row
                $headerRow = array_merge(
                    ['Student ID', 'Student Name'],
                    $assessmentNames,
                    ['Final Score', 'Academic Period', 'Academic Year']
                );
                fputcsv($file, $headerRow);

                // Write data rows
                foreach ($studentResults as $data) {
                    $row = [
                        $data['student']->student_id,
                        $data['student']->first_name . ' ' . $data['student']->last_name
                    ];

                    foreach ($assessmentNames as $assessment) {
                        $row[] = $data['scores'][$assessment] ?? '-';
                    }

                    $row[] = number_format($data['finalScore'], 2);
                    $row[] = $data['academicPeriod']->name;
                    $row[] = $data['academicYear']->name;

                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error("Results Export Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting results: ' . $e->getMessage());
        }
    }
}
