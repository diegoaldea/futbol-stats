<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatEvent extends Model
{
    protected $fillable = [
        'game_id',
        'game_player_id',
        'stat_id',
    ];

    public function gamePlayer()
    {
        return $this->belongsTo(GamePlayer::class);
    }

    public function stat()
    {
        return $this->belongsTo(Stat::class);
    }
}
