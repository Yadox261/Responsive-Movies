<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Movie;
use Livewire\WithFileUploads;

class MovieEditor extends Component
{
    use WithFileUploads;

    public $title, $director, $cast, $release_year, $genre, $duration, $synopsis, $movie_id;
    public $poster, $banner, $poster_url, $banner_url;
    public $isEdit = false;
    public $activeTab = 'general';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function mount(Movie $movie = null)
    {
        if ($movie && $movie->exists) {
            $this->isEdit = true;
            $this->movie_id = $movie->id;
            $this->title = $movie->title;
            $this->director = $movie->director;
            $this->cast = $movie->cast;
            $this->release_year = $movie->release_year;
            $this->genre = $movie->genre;
            $this->duration = $movie->duration;
            $this->synopsis = $movie->synopsis;
            $this->poster_url = $movie->poster_url;
            $this->banner_url = $movie->banner_url;
        }
    }

    public function render()
    {
        return view('livewire.movie-editor')->layout('layouts.app');
    }

    public function save()
    {
        $this->validate([
            'title' => 'required',
            'director' => 'required',
            'release_year' => 'required|numeric',
            'genre' => 'required',
            'synopsis' => 'required',
            'poster' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $this->title,
            'director' => $this->director,
            'cast' => $this->cast,
            'release_year' => $this->release_year,
            'genre' => $this->genre,
            'duration' => $this->duration,
            'synopsis' => $this->synopsis,
        ];

        if ($this->poster) {
            $data['poster_url'] = $this->poster->store('posters', 'public');
        }

        if ($this->banner) {
            $data['banner_url'] = $this->banner->store('banners', 'public');
        }

        Movie::updateOrCreate(['id' => $this->movie_id], $data);

        session()->flash('swal', [
            'title' => $this->isEdit ? '¡Actualizado!' : '¡Creado!',
            'text' => 'Película guardada con éxito.',
            'icon' => 'success'
        ]);

        return redirect()->route('movies.index');
    }
}
