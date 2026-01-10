<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    //
    protected $table = 'academic_years';
    protected $fillable = [
        'name',
        'status'
    ];

    public function fee ()
    {
        return $this->hasMany(Fee::class);
    }

}
