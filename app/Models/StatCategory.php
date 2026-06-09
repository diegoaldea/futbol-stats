<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function stats(){
        return $this->hasMany(Stat::class);
    }
}
