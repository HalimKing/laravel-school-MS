<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\LevelData;
use App\Models\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // In StudentController.php
    public function index(Request $request)
    {
        $search = strtolower($request->input('search'));

        $students = Student::with('latestLevel.class')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(student_id) LIKE ?', ["%{$search}%"]);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.student.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $classes = ClassModel::orderBy('name', 'asc')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        return view('admin.student.create', compact('classes', 'academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'other_name' => 'nullable|string',
            'address' => 'nullable|string',
            'gender' => 'required',
            'date_of_birth' => 'required',
            'student_id' => 'required|unique:students,student_id',
            'status' => 'required',
            'parent_name' => 'nullable|string',
            'parent_email' => 'nullable|email|unique:students,parent_email',
            'parent_phone' => 'nullable',
            'class_id' => 'required',
            'academic_year_id' => 'required',
        ]);

        try {
            $student = Student::create($validator);
            $levelData = new LevelData();
            $levelData->student_id = $student->id;
            $levelData->class_id = $request->class_id;
            $levelData->academic_year_id = $request->academic_year_id;
            $levelData->save();
            return redirect()->route('admin.students.index')->with('success', 'Student created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
        $classes = ClassModel::orderBy('name', 'asc')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        $levelData = LevelData::where('student_id', $student->id)->latest()->first();
        return view('admin.student.show', compact('classes', 'academicYears', 'student', 'levelData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        //
        $classes = ClassModel::orderBy('name', 'asc')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $levelData = LevelData::where('student_id', $student->id)->latest()->first();
        // dd($levelData);
        return view('admin.student.edit', compact('classes', 'academicYears', 'student', 'levelData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        //
        $validator = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'other_name' => 'nullable|string',
            'address' => 'nullable|string',
            'gender' => 'required',
            'date_of_birth' => 'required',
            'student_id' => 'required|unique:students,student_id,' . $student->id,
            'status' => 'required',
            'parent_name' => 'nullable|string',
            'parent_email' => 'nullable|email|unique:students,parent_email,' . $student->id,
            'parent_phone' => 'nullable',
            'class_id' => 'required',
            'academic_year_id' => 'required',
        ]);

        $student->update($validator);
        $levelData = LevelData::where('student_id', $student->id)->latest()->first();
        $levelData->update([
            'class_id' => $request->class_id,
            'academic_year_id' => $request->academic_year_id
        ]);
        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully');
    }

    /**
     * Display student enrollment report
     */
    public function report(Request $request)
    {
        $search = $request->input('search');
        $classId = $request->input('class_id');
        $academicYearId = $request->input('academic_year_id');
        $gender = $request->input('gender');
        $status = $request->input('status');

        $query = Student::with('latestLevel.classModel');

        // Apply filters
        if ($search) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(student_id) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($classId) {
            $query->whereHas('latestLevel', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        if ($academicYearId) {
            $query->whereHas('latestLevel', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            });
        }

        if ($gender) {
            $query->where('gender', $gender);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $students = $query->orderBy('first_name')->paginate(15)->withQueryString();

        $classes = ClassModel::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $genders = ['Male', 'Female'];

        // Calculate statistics
        $totalStudents = Student::count();
        $maleStudents = Student::where('gender', 'Male')->count();
        $femaleStudents = Student::where('gender', 'Female')->count();
        $activeStudents = Student::where('status', 'active')->count();

        return view('admin.reports.student-report', compact(
            'students',
            'classes',
            'academicYears',
            'genders',
            'totalStudents',
            'maleStudents',
            'femaleStudents',
            'activeStudents'
        ));
    }

    /**
     * Download student import template.
     */
    public function downloadImportTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="students-import-template.csv"',
        ];

        $columns = [
            'student_name',
            'student_id',
            'class',
            'gender',
            'date_of_birth',
            'email',
            'status',
            'parent_name',
            'parent_phone',
            'address',
            'academic_year',
        ];

        $classNames = ClassModel::orderBy('name')->pluck('name')->values();
        $yearName = AcademicYear::where('status', 'active')->value('name')
            ?: AcademicYear::latest('id')->value('name')
            ?: '';

        $sampleRows = [
            ['John Doe', 'STU-1001', $classNames->get(0, ''), 'Male', '2010-05-12', 'parent1@example.com', 'active', 'Mr Doe', '+123456789', 'Main Street', $yearName],
            ['Jane Smith', 'STU-1002', $classNames->get(1, $classNames->get(0, '')), 'Female', '2011-09-01', '', 'active', 'Mrs Smith', '+123456780', 'West Avenue', $yearName],
        ];

        return response()->stream(function () use ($columns, $sampleRows) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, $columns);
            foreach ($sampleRows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Import students from CSV or Excel data.
     */
    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required_without:rows_json|file|mimes:csv,txt,xlsx,xls|max:10240',
            'rows_json' => 'nullable|string',
            'duplicate_strategy' => 'required|in:skip,update',
        ]);

        try {
            $rows = $this->extractImportRows($request);

            if (empty($rows)) {
                return redirect()->back()->with('error', 'The uploaded file has no data rows to import.');
            }

            $activeAcademicYear = AcademicYear::where('status', 'active')->latest('id')->first();
            $fallbackAcademicYear = $activeAcademicYear ?: AcademicYear::latest('id')->first();

            if (!$fallbackAcademicYear) {
                return redirect()->back()->with('error', 'No academic year found. Please create an academic year before importing students.');
            }

            $summary = [
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => [],
            ];

            foreach ($rows as $index => $rawRow) {
                $rowNumber = $index + 2;
                $row = $this->normalizeRow($rawRow);

                try {
                    $this->processStudentImportRow(
                        $row,
                        $validated['duplicate_strategy'],
                        $fallbackAcademicYear,
                        $summary
                    );
                } catch (\Throwable $e) {
                    $summary['errors'][] = "Row {$rowNumber}: {$e->getMessage()}";
                }
            }

            $message = "Import completed. Created: {$summary['created']}, Updated: {$summary['updated']}, Skipped: {$summary['skipped']}";
            if (count($summary['errors']) > 0) {
                $message .= ', Errors: ' . count($summary['errors']);
            }

            $redirect = redirect()->route('admin.students.index')->with('success', $message);

            if (!empty($summary['errors'])) {
                $errorPreview = implode(' | ', array_slice($summary['errors'], 0, 8));
                if (count($summary['errors']) > 8) {
                    $errorPreview .= ' | ...';
                }
                $redirect->with('error', $errorPreview);
            }

            return $redirect;
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function extractImportRows(Request $request): array
    {
        if ($request->filled('rows_json')) {
            $decoded = json_decode($request->input('rows_json'), true);
            if (!is_array($decoded)) {
                throw new \RuntimeException('Invalid Excel payload. Please try again.');
            }

            return array_values(array_filter($decoded, function ($row) {
                return is_array($row);
            }));
        }

        /** @var UploadedFile|null $file */
        $file = $request->file('file');
        if (!$file) {
            throw new \RuntimeException('No import file was uploaded.');
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        if (in_array($extension, ['csv', 'txt'], true)) {
            return $this->parseCsvToAssociativeRows($file->getRealPath());
        }

        if (in_array($extension, ['xlsx', 'xls'], true)) {
            $ioFactoryClass = 'PhpOffice\\PhpSpreadsheet\\IOFactory';
            if (!class_exists($ioFactoryClass)) {
                throw new \RuntimeException('Excel parsing package is not available on the server. Use CSV file or keep the Excel file selected and submit again from the import form (browser-side parsing).');
            }

            return $this->parseSpreadsheetToAssociativeRows($file->getRealPath());
        }

        throw new \RuntimeException('Unsupported file format.');
    }

    private function parseCsvToAssociativeRows(string $path): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new \RuntimeException('Unable to read the uploaded CSV file.');
        }

        $headers = null;
        $rows = [];

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if ($headers === null) {
                $headers = array_map(fn($header) => $this->normalizeHeader((string) $header), $data);
                continue;
            }

            if (count(array_filter($data, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $row = [];
            foreach ($headers as $index => $header) {
                $row[$header] = isset($data[$index]) ? trim((string) $data[$index]) : null;
            }

            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function parseSpreadsheetToAssociativeRows(string $path): array
    {
        $ioFactoryClass = 'PhpOffice\\PhpSpreadsheet\\IOFactory';
        $spreadsheet = $ioFactoryClass::load($path);
        $sheet = $spreadsheet->getSheet(0);
        $sheetRows = $sheet->toArray(null, true, true, false);

        if (empty($sheetRows)) {
            return [];
        }

        $headers = array_map(fn($header) => $this->normalizeHeader((string) $header), array_shift($sheetRows));
        $rows = [];

        foreach ($sheetRows as $dataRow) {
            if (count(array_filter($dataRow, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $row = [];
            foreach ($headers as $index => $header) {
                $value = $dataRow[$index] ?? null;
                $row[$header] = is_string($value) ? trim($value) : $value;
            }
            $rows[] = $row;
        }

        return $rows;
    }

    private function normalizeHeader(string $header): string
    {
        $header = strtolower(trim($header));
        $header = preg_replace('/[^a-z0-9]+/', '_', $header);
        return trim((string) $header, '_');
    }

    private function normalizeRow(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $normalized[$this->normalizeHeader((string) $key)] = is_string($value) ? trim($value) : $value;
        }
        return $normalized;
    }

    private function processStudentImportRow(array $row, string $duplicateStrategy, AcademicYear $defaultAcademicYear, array &$summary): void
    {
        $studentId = $this->pickValue($row, ['student_id', 'admission_number', 'id_number']);
        if (!$studentId) {
            throw new \RuntimeException('student_id is required.');
        }

        $fullName = $this->pickValue($row, ['student_name', 'name', 'full_name']);
        $firstName = $this->pickValue($row, ['first_name']);
        $lastName = $this->pickValue($row, ['last_name']);

        if ((!$firstName || !$lastName) && $fullName) {
            $parts = preg_split('/\s+/', trim($fullName)) ?: [];
            $firstName = $firstName ?: ($parts[0] ?? null);
            $lastName = $lastName ?: (count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : null);
        }

        if (!$firstName || !$lastName) {
            throw new \RuntimeException('student_name (or first_name and last_name) is required.');
        }

        $classToken = $this->pickValue($row, ['class', 'class_name', 'class_id']);
        if (!$classToken) {
            throw new \RuntimeException('class is required.');
        }

        $class = $this->resolveClassFromToken((string) $classToken);

        if (!$class) {
            $available = ClassModel::orderBy('name')->pluck('name')->take(8)->implode(', ');
            throw new \RuntimeException("class '{$classToken}' was not found. Available examples: {$available}");
        }

        $academicYearToken = $this->pickValue($row, ['academic_year', 'academic_year_name', 'academic_year_id']);
        $academicYear = $defaultAcademicYear;
        if ($academicYearToken) {
            $academicYear = ctype_digit((string) $academicYearToken)
                ? AcademicYear::find((int) $academicYearToken)
                : AcademicYear::whereRaw('LOWER(name) = ?', [strtolower((string) $academicYearToken)])->first();
            if (!$academicYear) {
                throw new \RuntimeException("academic_year '{$academicYearToken}' was not found.");
            }
        }

        $genderRaw = strtolower((string) ($this->pickValue($row, ['gender', 'sex']) ?? ''));
        $gender = $genderRaw === 'female' ? 'Female' : ($genderRaw === 'male' ? 'Male' : null);
        if (!$gender) {
            throw new \RuntimeException("gender is required and must be 'Male' or 'Female'.");
        }

        $dob = $this->pickValue($row, ['date_of_birth', 'dob', 'birth_date']);
        if (!$dob) {
            throw new \RuntimeException('date_of_birth is required (YYYY-MM-DD).');
        }

        try {
            $parsedDob = Carbon::parse((string) $dob)->format('Y-m-d');
        } catch (\Throwable $e) {
            throw new \RuntimeException('date_of_birth is invalid. Use YYYY-MM-DD.');
        }

        $statusRaw = strtolower((string) ($this->pickValue($row, ['status']) ?? 'active'));
        $status = in_array($statusRaw, ['active', 'inactive'], true) ? $statusRaw : null;
        if (!$status) {
            throw new \RuntimeException("status must be 'active' or 'inactive'.");
        }

        $email = $this->pickValue($row, ['email', 'parent_email', 'guardian_email']);
        if ($email) {
            $emailValidator = Validator::make(['email' => $email], ['email' => 'nullable|email']);
            if ($emailValidator->fails()) {
                throw new \RuntimeException('email format is invalid.');
            }
        }

        $existing = Student::where('student_id', $studentId)->first();
        if ($existing && $duplicateStrategy === 'skip') {
            $summary['skipped']++;
            return;
        }

        DB::transaction(function () use (
            $row,
            $studentId,
            $firstName,
            $lastName,
            $gender,
            $parsedDob,
            $status,
            $email,
            $class,
            $academicYear,
            $existing,
            &$summary
        ) {
            $payload = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'middle_name' => $this->pickValue($row, ['middle_name']),
                'other_name' => $this->pickValue($row, ['other_name', 'other_names']),
                'address' => $this->pickValue($row, ['address']),
                'gender' => $gender,
                'date_of_birth' => $parsedDob,
                'student_id' => $studentId,
                'status' => $status,
                'parent_name' => $this->pickValue($row, ['parent_name', 'guardian_name']),
                'parent_phone' => $this->pickValue($row, ['parent_phone', 'guardian_phone']),
                'parent_email' => $email,
            ];

            if ($existing) {
                $payload['parent_email'] = $email ?: $existing->parent_email;
                $existing->update($payload);
                $student = $existing;
                $summary['updated']++;
            } else {
                $student = Student::create($payload);
                $summary['created']++;
            }

            $levelData = LevelData::where('student_id', $student->id)->latest('id')->first();
            if ($levelData) {
                $levelData->update([
                    'class_id' => $class->id,
                    'academic_year_id' => $academicYear->id,
                ]);
            } else {
                LevelData::create([
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'academic_year_id' => $academicYear->id,
                ]);
            }
        });
    }

    private function pickValue(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row)) {
                $value = is_string($row[$key]) ? trim($row[$key]) : $row[$key];
                if ($value !== null && $value !== '') {
                    return (string) $value;
                }
            }
        }

        return null;
    }

    private function resolveClassFromToken(string $classToken): ?ClassModel
    {
        $token = trim($classToken);
        if ($token === '') {
            return null;
        }

        if (ctype_digit($token)) {
            $byId = ClassModel::find((int) $token);
            if ($byId) {
                return $byId;
            }
        }

        $exact = ClassModel::whereRaw('LOWER(name) = ?', [strtolower($token)])->first();
        if ($exact) {
            return $exact;
        }

        $normalizedToken = $this->normalizeClassLabel($token);
        $classes = ClassModel::select('id', 'name')->get();

        foreach ($classes as $class) {
            if ($this->normalizeClassLabel((string) $class->name) === $normalizedToken) {
                return $class;
            }
        }

        // Fallback: map forms/sections by numeric part (e.g. Form 1A -> Class 1)
        if (preg_match('/(\d+)/', $token, $matches)) {
            $number = $matches[1];
            foreach ($classes as $class) {
                if (preg_match('/\b' . preg_quote($number, '/') . '\b/i', (string) $class->name)) {
                    return $class;
                }
            }
        }

        return null;
    }

    private function normalizeClassLabel(string $label): string
    {
        $normalized = strtolower(trim($label));
        $normalized = str_replace(['form', 'grade', 'std', 'standard', 'year'], 'class', $normalized);
        $normalized = preg_replace('/[^a-z0-9]+/', '', $normalized);
        return (string) $normalized;
    }
}
