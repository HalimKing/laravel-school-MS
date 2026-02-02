<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignSubject extends Model
{
    //
    protected $fillable = [
        'teacher_id',   
        'subject_id',
        'class_id',
        'academic_year_id',
        'status',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
    public function manySubjects()
    {
        return $this->belongsToMany(Subject::class, 'assign_subjects', 'id', 'subject_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
    public function manyClasses()
    {
        return $this->belongsToMany(ClassModel::class, 'assign_subjects', 'id', 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
