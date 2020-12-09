<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'results';

    protected $fillable = [
        'gdb', 'g1', 'g2', 'g3', 'g4', 'g5', 'g6', 'g7', 'g8', 'loto', 'region_id', 'province_id', 'date'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function loto()
    {
        return $this->hasMany(Loto::class);
    }
}
