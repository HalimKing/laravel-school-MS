<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    //
    protected $fillable = [
        'name',
        'percentage',
        'subject_id',
        'class_id',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }


}
