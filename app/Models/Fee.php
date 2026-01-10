<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    //
    protected $fillable = [
        'fee_category_id',
        'academic_year_id',
        'academic_period_id',
        'class_id',
        'amount'
    ];

    public function feeCategory()
    {
        return $this->belongsTo(FeeCategory::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function academicPeriod()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }
}
