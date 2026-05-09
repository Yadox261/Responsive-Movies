<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Movie;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::all()->map(function ($movie) {
            return [
                'id' => $movie->id,
                'title' => $movie->title,
                'director' => $movie->director,
                'cast' => $movie->cast,
                'release_year' => $movie->release_year,
                'genre' => $movie->genre,
                'duration' => $movie->duration,
                'synopsis' => $movie->synopsis,
                'poster_url' => $movie->poster_url ? asset('storage/' . $movie->poster_url) : null,
                'banner_url' => $movie->banner_url ? asset('storage/' . $movie->banner_url) : null,
            ];
        });

        return response()->json($movies);
    }
}
