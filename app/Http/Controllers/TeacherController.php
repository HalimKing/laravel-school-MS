<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = strtolower($request->input('search'));

        $teachers = Teacher::when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(staff_id) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.teacher.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.teacher.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'staff_id' => 'required|unique:teachers,staff_id',
            'email' => 'required|email|unique:teachers,email',
            'phone' => 'required',
            'status' => 'required',
            'address' => 'nullable|string',
        ]);
        $password = Hash::make('password');
        Teacher::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'staff_id' => $request->staff_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'password' => $password,
            'address' => $request->address,
        ]);
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
        return view('admin.teacher.show', compact('teacher'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
        return view('admin.teacher.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        //
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'staff_id' => 'required|unique:teachers,staff_id,'.$teacher->id,
            'email' => 'required|email|unique:teachers,email,'.$teacher->id,
            'phone' => 'required',
            'status' => 'required',
            'address' => 'nullable|string',
        ]);
        $teacher->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'staff_id' => $request->staff_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'address' => $request->address,
        ]);
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        //
        $teacher->delete();
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully');
    }


        /**
     * Update the teacher's password in the database.
     */
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        try {
            $teacher = Teacher::findOrFail($id);
            
            $teacher->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()
                ->route('admin.teacher.password.index')
                ->with('success', "Password for {$teacher->first_name} has been reset successfully.");
                
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while resetting the password. Please try again.');
        }
    }


    public function search(Request $request)
    {
        $request->validate([
            'search_query' => 'required|string',
        ]);

        $query = $request->input('search_query');

        // Search by email or staff_id
        $teacher = Teacher::where('email', $query)
            ->orWhere('staff_id', $query)
            ->first();

        return view('admin.teacher.password-management', compact('teacher'));
    }

    /**
     * Download teacher import template.
     */
    public function downloadImportTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="teachers-import-template.csv"',
        ];

        $columns = [
            'teacher_name',
            'staff_id',
            'email',
            'phone',
            'gender',
            'status',
            'address',
        ];

        $sampleRows = [
            ['John Doe', 'STAFF-001', 'john@school.com', '+123456789', 'Male', 'active', 'Main Street'],
            ['Jane Smith', 'STAFF-002', 'jane@school.com', '+123456780', 'Female', 'active', 'West Avenue'],
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
     * Import teachers from CSV or Excel data.
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
                    $this->processTeacherImportRow(
                        $row,
                        $validated['duplicate_strategy'],
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

            \App\Helpers\SystemLogHelper::log('Import Teachers', 'Teacher Management', $message);

            $redirect = redirect()->route('admin.teachers.index')->with('success', $message);

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

    private function processTeacherImportRow(array $row, string $duplicateStrategy, array &$summary): void
    {
        $staffId = $this->pickValue($row, ['staff_id', 'id_number', 'employee_id']);
        if (!$staffId) {
            throw new \RuntimeException('staff_id is required.');
        }

        $fullName = $this->pickValue($row, ['teacher_name', 'name', 'full_name']);
        $firstName = $this->pickValue($row, ['first_name']);
        $lastName = $this->pickValue($row, ['last_name']);

        if ((!$firstName || !$lastName) && $fullName) {
            $parts = preg_split('/\s+/', trim($fullName)) ?: [];
            $firstName = $firstName ?: ($parts[0] ?? null);
            $lastName = $lastName ?: (count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : null);
        }

        if (!$firstName || !$lastName) {
            throw new \RuntimeException('teacher_name (or first_name and last_name) is required.');
        }

        $email = $this->pickValue($row, ['email']);
        if (!$email) {
            throw new \RuntimeException('email is required.');
        }

        $emailValidator = Validator::make(['email' => $email], ['email' => 'required|email']);
        if ($emailValidator->fails()) {
            throw new \RuntimeException('email format is invalid.');
        }

        $phone = $this->pickValue($row, ['phone', 'phone_number', 'mobile']);
        if (!$phone) {
            throw new \RuntimeException('phone is required.');
        }

        $statusRaw = strtolower((string) ($this->pickValue($row, ['status']) ?? 'active'));
        $status = in_array($statusRaw, ['active', 'inactive'], true) ? $statusRaw : null;
        if (!$status) {
            throw new \RuntimeException("status must be 'active' or 'inactive'.");
        }

        $existing = Teacher::where('staff_id', $staffId)->first();
        if ($existing && $duplicateStrategy === 'skip') {
            $summary['skipped']++;
            return;
        }

        if ($existing && $duplicateStrategy === 'update') {
            // Check if email is unique for other teachers
            $emailExists = Teacher::where('email', $email)
                ->where('id', '!=', $existing->id)
                ->exists();
            
            if ($emailExists) {
                throw new \RuntimeException('email is already used by another teacher.');
            }
        } else if (!$existing) {
            // For new teachers, check if email is unique
            $emailExists = Teacher::where('email', $email)->exists();
            if ($emailExists) {
                throw new \RuntimeException('email is already used by another teacher.');
            }
        }

        DB::transaction(function () use (
            $row,
            $staffId,
            $firstName,
            $lastName,
            $email,
            $phone,
            $status,
            $existing,
            &$summary
        ) {
            $payload = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'staff_id' => $staffId,
                'email' => $email,
                'phone' => $phone,
                'status' => $status,
                'address' => $this->pickValue($row, ['address']),
            ];

            if ($existing) {
                $existing->update($payload);
                $summary['updated']++;
            } else {
                $payload['password'] = Hash::make('password');
                Teacher::create($payload);
                $summary['created']++;
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
}
