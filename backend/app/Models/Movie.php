<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
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
    ];
}
