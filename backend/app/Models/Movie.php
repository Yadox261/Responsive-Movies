<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'director',
        'cast',
        'release_year',
        'genre',
        'duration',
        'synopsis',
        'poster_url',
        'banner_url',
        'is_archived',
        'is_premiere',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'is_premiere' => 'boolean',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
