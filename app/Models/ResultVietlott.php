<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultVietlott extends Model
{
    protected $table = 'result_vietlotts';

    protected $fillable = [
        'number', 'ticket', 'vietlott_id', 'g1', 'g2', 'g3', 'gkk1', 'gkk2', 'date', 'jackpot', 'jackpot1', 'jackpot2', 'g4', 'g5', 'g6', 'g7', 'amount_3d', 'amount_3d_plus'
    ];

    public function vietlott()
    {
        return $this->belongsTo(Vietlott::class);
    }
}
