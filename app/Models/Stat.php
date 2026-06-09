<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    protected $fillable = [
    'name',
    'stat_category_id',
    'points',
    'is_global',
    'user_id',
    ];

    protected $casts = [
        'is_global' => 'boolean',
        'points' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(StatCategory::class);
    }
}
