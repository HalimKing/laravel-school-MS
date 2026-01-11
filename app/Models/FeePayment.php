<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    //
    protected $fillable = [
        'level_data_id', 'fee_id', 'amount', 'payment_date', 'payment_method', 'reference_no', 'remarks'
    ];

    protected $casts = [
        'payment_date' => 'date', // or 'datetime'
    ];

    public function levelData()
    {
        return $this->belongsTo(LevelData::class);
    }

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

}
