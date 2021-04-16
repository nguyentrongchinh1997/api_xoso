<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Chat extends Model
{
    protected $table = 'chats';

    protected $fillable = [
        'user_id',
        'content',
        'region_id',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
