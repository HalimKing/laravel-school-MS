<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'staff_id',
        'email',
        'phone',
        'status',
        'password',
        'address',
    ];
}
