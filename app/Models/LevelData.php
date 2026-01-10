<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelData extends Model
{
    //
    protected $fillable = [
        'class_id',
        'academic_year_id',
        'student_id'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function latestClass()
    {
        return $this->hasOne(ClassModel::class)->latestOfMany();
    }
}
