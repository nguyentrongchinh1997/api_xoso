<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loto extends Model
{
    protected $table = 'lotos';

    protected $fillable = [
        'number', 'result_id', 'date', 'region_id', 'province_id'
    ];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }
}
