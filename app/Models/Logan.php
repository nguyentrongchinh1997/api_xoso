<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logan extends Model
{
    protected $table = 'logans';

    protected $fillable = [
        'loto',
        'number',
        'date'
    ];
}
