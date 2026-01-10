<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeCategory extends Model
{
    //
    protected $fillable = [
        'name',
        'description'
    ];

    public function fee ()
    {
        return $this->hasMany(Fee::class);
    }
}
