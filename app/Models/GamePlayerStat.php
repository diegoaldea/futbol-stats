<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamePlayerStat extends Model
{
    protected $fillable = [
        'game_player_id',
        'stat_id',
        'value',
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
