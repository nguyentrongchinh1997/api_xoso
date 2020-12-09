<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vietlott extends Model
{
    protected $table = 'vietlotts';

    protected $fillable = ['name', 'slug'];

    public function resultVietlott()
    {
        return $this->hasMany(ResultVietlott::class);
    }
}
