<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Xsdt extends Model
{
    protected $table = 'xsdts';

    protected $fillable = [
        'dt123', 'dt6x36', 'dt4', 'date'
    ];
}
