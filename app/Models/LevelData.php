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

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function class()
    {
        return $this->classModel();
    }

    public function latestClass()
    {
        return $this->hasOne(ClassModel::class)->latestOfMany();
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }
}
