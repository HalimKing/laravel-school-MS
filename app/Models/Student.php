<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'other_name',
        'gender',
        'date_of_birth',
        'student_id',
        'address',
        'status',

        // parent/guardian details
        'parent_name',
        'parent_phone',
        'parent_email',
    ];

    public function levels()
    {
        return $this->hasMany(LevelData::class);
    }


    public function latestLevel()
    {
        return $this->hasOne(LevelData::class)->latestOfMany();
    }
}
