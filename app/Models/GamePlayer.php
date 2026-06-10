<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GamePlayerStat;

class GamePlayer extends Model
{
    protected $fillable = [
    'game_id',
    'player_id',
    'team',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function stats()
    {
        return $this->hasMany(GamePlayerStat::class);
    }

    
}
