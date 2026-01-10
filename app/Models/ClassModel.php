<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    //
    protected $fillable = [
        'name',
        'description'
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function levels()
    {
        return $this->hasMany(LevelData::class);
    }

    public function fee ()
    {
        return $this->hasMany(Fee::class);
    }
}
