<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    //
    protected $fillable = [
        'code',
        'name',
        'type'
    ];

    public function assignSubjects()
    {
        return $this->belongsToMany(AssignSubject::class);
    }
}
