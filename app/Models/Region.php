<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regions';

    protected $fillable = [
        'name', 'slug'
    ];

    public function result()
    {
        return $this->hasMany(Result::class);
    }
}
