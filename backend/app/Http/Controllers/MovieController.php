<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Movie;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::with('schedules')->where('is_archived', false)->get()->map(function ($movie) {
            return [
                'id' => $movie->id,
                'title' => $movie->title,
                'director' => $movie->director,
                'cast' => $movie->cast,
                'release_year' => $movie->release_year,
                'genre' => $movie->genre,
                'duration' => $movie->duration,
                'synopsis' => $movie->synopsis,
                'poster_url' => $movie->poster_url ? (str_starts_with($movie->poster_url, 'http') ? $movie->poster_url : asset('storage/' . $movie->poster_url)) : null,
                'banner_url' => $movie->banner_url ? (str_starts_with($movie->banner_url, 'http') ? $movie->banner_url : asset('storage/' . $movie->banner_url)) : null,
                'schedules' => $movie->schedules->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'day' => $schedule->day,
                        'time' => $schedule->time,
                        'room' => $schedule->room,
                        'format' => $schedule->format,
                    ];
                }),
            ];
        });

        return response()->json($movies);
    }
}
