<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dream extends Model
{
    protected $table = 'dreams';

    protected $fillable = [
        'text',
        'number'
    ];
}
