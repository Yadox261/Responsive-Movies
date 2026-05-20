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
        $inception = \App\Models\Movie::updateOrCreate(['title' => 'Inception'], [
            'director' => 'Christopher Nolan',
            'release_year' => 2010,
            'genre' => 'Sci-Fi',
            'duration' => '2h 28min',
            'synopsis' => 'A thief who steals corporate secrets through the use of dream-sharing technology.',
            'poster_url' => 'https://example.com/inception.jpg'
        ]);

        $inception->schedules()->updateOrCreate(
            ['time' => '16:00', 'room' => 'Sala 1'],
            ['day' => 'Lunes a Viernes', 'format' => '2D SUB']
        );
        $inception->schedules()->updateOrCreate(
            ['time' => '20:30', 'room' => 'Sala 2'],
            ['day' => 'Fin de Semana', 'format' => '3D ESP']
        );

        $matrix = \App\Models\Movie::updateOrCreate(['title' => 'The Matrix'], [
            'director' => 'Lana Wachowski, Lilly Wachowski',
            'release_year' => 1999,
            'genre' => 'Action',
            'duration' => '2h 16min',
            'synopsis' => 'A computer hacker learns from mysterious rebels about the true nature of his reality.',
            'poster_url' => 'https://example.com/matrix.jpg'
        ]);

        $matrix->schedules()->updateOrCreate(
            ['time' => '14:30', 'room' => 'Sala 3'],
            ['day' => 'Todos los días', 'format' => '2D ESP']
        );
        $matrix->schedules()->updateOrCreate(
            ['time' => '18:00', 'room' => 'Sala 3'],
            ['day' => 'Todos los días', 'format' => '2D SUB']
        );

        $interstellar = \App\Models\Movie::updateOrCreate(['title' => 'Interstellar'], [
            'director' => 'Christopher Nolan',
            'release_year' => 2014,
            'genre' => 'Adventure',
            'duration' => '2h 49min',
            'synopsis' => 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity\'s survival.',
            'poster_url' => 'https://example.com/interstellar.jpg'
        ]);

        $interstellar->schedules()->updateOrCreate(
            ['time' => '17:00', 'room' => 'Sala IMAX'],
            ['day' => 'Lunes a Viernes', 'format' => 'IMAX SUB']
        );
        $interstellar->schedules()->updateOrCreate(
            ['time' => '21:00', 'room' => 'Sala IMAX'],
            ['day' => 'Fin de Semana', 'format' => 'IMAX SUB']
        );

        $deadpool = \App\Models\Movie::updateOrCreate(['title' => 'Deadpool & Wolverine'], [
            'director' => 'Shawn Levy',
            'release_year' => 2025,
            'genre' => 'Action/Sci-Fi',
            'duration' => '2h 7min',
            'synopsis' => 'La Autoridad de Variación Temporal (TVA) le informa a Wade Wilson que su universo está condenado a desaparecer tras la muerte de Logan. Para salvar a su mundo, Deadpool une fuerzas a regañadientes con una variante de Wolverine, desatando una caótica misión a través del multiverso',
            'poster_url' => 'posters/Et9n7bowyyOVcATdeygTi1qPhjFuHUsfHaY6i9sH.jpg',
            'banner_url' => 'banners/xcQifgEI2MVzFPjYM4wrNsl5DfTO9HovUblLgplN.jpg'
        ]);

        $deadpool->schedules()->updateOrCreate(
            ['time' => '15:30', 'room' => 'Sala 5'],
            ['day' => 'Todos los días', 'format' => '2D ESP']
        );
        $deadpool->schedules()->updateOrCreate(
            ['time' => '18:45', 'room' => 'Sala 4'],
            ['day' => 'Todos los días', 'format' => '3D ESP']
        );
        $deadpool->schedules()->updateOrCreate(
            ['time' => '22:00', 'room' => 'Sala 4'],
            ['day' => 'Fin de Semana', 'format' => '2D SUB']
        );
    }
}
