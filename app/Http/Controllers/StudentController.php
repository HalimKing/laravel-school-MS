<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\LevelData;
use App\Models\Student;
use Illuminate\Http\Request;

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
                return $query->where(function($q) use ($search) {
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
}
