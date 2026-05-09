<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Movie::create([
            'title' => 'Inception',
            'director' => 'Christopher Nolan',
            'release_year' => 2010,
            'genre' => 'Sci-Fi',
            'synopsis' => 'A thief who steals corporate secrets through the use of dream-sharing technology.',
            'poster_url' => 'https://example.com/inception.jpg'
        ]);

        \App\Models\Movie::create([
            'title' => 'The Matrix',
            'director' => 'Lana Wachowski, Lilly Wachowski',
            'release_year' => 1999,
            'genre' => 'Action',
            'synopsis' => 'A computer hacker learns from mysterious rebels about the true nature of his reality.',
            'poster_url' => 'https://example.com/matrix.jpg'
        ]);

        \App\Models\Movie::create([
            'title' => 'Interstellar',
            'director' => 'Christopher Nolan',
            'release_year' => 2014,
            'genre' => 'Adventure',
            'synopsis' => 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity\'s survival.',
            'poster_url' => 'https://example.com/interstellar.jpg'
        ]);
    }
}
