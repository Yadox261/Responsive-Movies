<?php

namespace Tests\Feature;

use App\Livewire\MovieEditor;
use App\Models\Movie;
use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MovieSchedulingTest extends TestCase
{
    use RefreshDatabase;

    public function test_normal_movie_respects_time_boundaries()
    {
        $movie = Movie::create([
            'title' => 'Pelicula Regular',
            'director' => 'Director',
            'release_year' => 2026,
            'genre' => 'Accion',
            'duration' => '2h',
            'synopsis' => 'Una gran sinopsis',
            'is_premiere' => false,
        ]);

        // Attempting to schedule at 10:30 AM (before 11:00 AM) should fail
        Livewire::test(MovieEditor::class, ['movie' => $movie])
            ->set('new_day', 'Lunes')
            ->set('new_time', '10:30')
            ->set('new_room', 'Sala 1')
            ->call('addSchedule')
            ->assertHasErrors(['new_time']);

        // Attempting to schedule at 9:15 PM (after 9:00 PM) should fail
        Livewire::test(MovieEditor::class, ['movie' => $movie])
            ->set('new_day', 'Lunes')
            ->set('new_time', '21:15')
            ->set('new_room', 'Sala 1')
            ->call('addSchedule')
            ->assertHasErrors(['new_time']);

        // Attempting to schedule at 8:00 PM (before 9:00 PM) should pass
        Livewire::test(MovieEditor::class, ['movie' => $movie])
            ->set('new_day', 'Lunes')
            ->set('new_time', '20:00')
            ->set('new_room', 'Sala 1')
            ->call('addSchedule')
            ->assertHasNoErrors();
    }

    public function test_premiere_movie_allows_schedules_up_to_11_pm()
    {
        $movie = Movie::create([
            'title' => 'Pelicula Estreno',
            'director' => 'Director',
            'release_year' => 2026,
            'genre' => 'Accion',
            'duration' => '2h',
            'synopsis' => 'Una gran sinopsis',
            'is_premiere' => true,
        ]);

        // Attempting to schedule at 10:30 PM (before 11:00 PM) should succeed for premiere
        Livewire::test(MovieEditor::class, ['movie' => $movie])
            ->set('new_day', 'Lunes')
            ->set('new_time', '22:30')
            ->set('new_room', 'Sala 1')
            ->call('addSchedule')
            ->assertHasNoErrors();

        // Attempting to schedule at 11:15 PM (after 11:00 PM) should fail
        Livewire::test(MovieEditor::class, ['movie' => $movie])
            ->set('new_day', 'Lunes')
            ->set('new_time', '23:15')
            ->set('new_room', 'Sala 1')
            ->call('addSchedule')
            ->assertHasErrors(['new_time']);
    }

    public function test_schedule_prevents_overlap_collisions_with_15_minute_buffer()
    {
        $movie1 = Movie::create([
            'title' => 'Pelicula Uno',
            'director' => 'Director',
            'release_year' => 2026,
            'genre' => 'Accion',
            'duration' => '2h 00min', // 120 minutes + 15 min buffer = 135 mins total (2h 15min)
            'synopsis' => 'Una gran sinopsis',
            'is_premiere' => false,
        ]);

        $movie2 = Movie::create([
            'title' => 'Pelicula Dos',
            'director' => 'Director',
            'release_year' => 2026,
            'genre' => 'Accion',
            'duration' => '1h 30min',
            'synopsis' => 'Una gran sinopsis',
            'is_premiere' => false,
        ]);

        // Schedule Movie 1 on Lunes at 15:00 in Sala 1 (occupies until 17:15)
        Schedule::create([
            'movie_id' => $movie1->id,
            'day' => 'Lunes',
            'time' => '15:00',
            'room' => 'Sala 1',
            'format' => '2D Español',
        ]);

        // Attempt to schedule Movie 2 on Lunes at 16:00 in Sala 1 (overlaps) should fail
        Livewire::test(MovieEditor::class, ['movie' => $movie2])
            ->set('new_day', 'Lunes')
            ->set('new_time', '16:00')
            ->set('new_room', 'Sala 1')
            ->call('addSchedule')
            ->assertHasErrors(['new_time']);

        // Attempt to schedule Movie 2 on Lunes at 17:10 in Sala 1 (within the 15-min buffer) should fail
        Livewire::test(MovieEditor::class, ['movie' => $movie2])
            ->set('new_day', 'Lunes')
            ->set('new_time', '17:10')
            ->set('new_room', 'Sala 1')
            ->call('addSchedule')
            ->assertHasErrors(['new_time']);

        // Attempt to schedule Movie 2 on Lunes at 17:15 in Sala 1 (just after buffer) should pass
        Livewire::test(MovieEditor::class, ['movie' => $movie2])
            ->set('new_day', 'Lunes')
            ->set('new_time', '17:15')
            ->set('new_room', 'Sala 1')
            ->call('addSchedule')
            ->assertHasNoErrors();
    }
}
