<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'day',
        'time',
        'room',
        'format',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
