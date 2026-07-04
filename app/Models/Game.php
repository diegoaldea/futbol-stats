<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GamePlayer;

class Game extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'team_a_name',
        'team_b_name',
        'date',
        'score_team_a',
        'score_team_b',
        'finished_at',
    ];

    protected $casts = [
        'date' => 'date',
        'finished_at' => 'datetime',
    ];

    public function isFinished(): bool
    {
        return $this->finished_at !== null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isGuest(): bool
    {
        return $this->user_id === null;
    }

    public function gamePlayers()
    {
        return $this->hasMany(GamePlayer::class);
    }
}
