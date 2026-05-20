<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Movie;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MovieManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $title, $director, $cast, $release_year, $genre, $duration, $synopsis, $movie_id;
    public $poster, $banner, $poster_url, $banner_url;
    public $isOpen = false;
    public $status = 'todos';

    // Propiedades de búsqueda y ordenamiento
    public $search = '';
    public $sortBy = 'title';
    public $sortDir = 'asc';
    public $is_premiere = false;

    protected $listeners = ['deleteMovie' => 'delete'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDir = 'asc';
        }
    }

    public function render()
    {
        $query = Movie::query();

        if ($this->status == 'archivadas') {
            $query->where('is_archived', true);
        } else {
            $query->where('is_archived', false);
            
            if ($this->status == 'cartelera') {
                $query->where('release_year', '<=', 2026);
            } elseif ($this->status == 'proximamente') {
                $query->where('release_year', '>', 2026);
            }
        }

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('genre', 'like', '%' . $this->search . '%')
                  ->orWhere('director', 'like', '%' . $this->search . '%');
            });
        }

        $allowedSorts = ['title', 'director', 'release_year', 'duration'];
        $sortField = in_array($this->sortBy, $allowedSorts) ? $this->sortBy : 'title';
        $sortDirection = $this->sortDir === 'desc' ? 'desc' : 'asc';

        $query->orderBy($sortField, $sortDirection);

        return view('livewire.movie-manager', [
            'movies' => $query->paginate(10)
        ])->layout('layouts.app');
    }

    public function toggleArchive($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->is_archived = !$movie->is_archived;
        $movie->save();

        $this->dispatch('swal:modal', [
            'title' => $movie->is_archived ? '¡Archivada!' : '¡Restaurada!',
            'text' => $movie->is_archived ? 'Película movida al archivo.' : 'Película devuelta a cartelera.',
            'icon' => 'success'
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->title = '';
        $this->director = '';
        $this->release_year = '';
        $this->genre = '';
        $this->synopsis = '';
        $this->duration = '';
        $this->cast = '';
        $this->poster = null;
        $this->banner = null;
        $this->poster_url = null;
        $this->banner_url = null;
        $this->movie_id = '';
        $this->is_premiere = false;
    }

    public function store()
    {
        $this->validate([
            'title' => 'required',
            'director' => 'required',
            'release_year' => 'required|numeric',
            'genre' => 'required',
            'duration' => 'nullable',
            'cast' => 'nullable',
            'synopsis' => 'required',
            'poster' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $this->title,
            'director' => $this->director,
            'release_year' => $this->release_year,
            'genre' => $this->genre,
            'duration' => $this->duration,
            'cast' => $this->cast,
            'synopsis' => $this->synopsis,
            'is_premiere' => (bool) $this->is_premiere,
        ];

        if ($this->poster) {
            $data['poster_url'] = $this->poster->store('posters', 'public');
        }

        if ($this->banner) {
            $data['banner_url'] = $this->banner->store('banners', 'public');
        }

        Movie::updateOrCreate(['id' => $this->movie_id], $data);

        $this->dispatch('swal:modal', [
            'title' => $this->movie_id ? '¡Actualizado!' : '¡Creado!',
            'text' => 'Película guardada con éxito.',
            'icon' => 'success'
        ]);

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $movie = Movie::findOrFail($id);
        $this->movie_id = $id;
        $this->title = $movie->title;
        $this->director = $movie->director;
        $this->release_year = $movie->release_year;
        $this->genre = $movie->genre;
        $this->duration = $movie->duration;
        $this->cast = $movie->cast;
        $this->synopsis = $movie->synopsis;
        $this->poster_url = $movie->poster_url;
        $this->banner_url = $movie->banner_url;
        $this->is_premiere = $movie->is_premiere;

        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => '¿Estás seguro?',
            'text' => '¡No podrás revertir esta acción!',
            'icon' => 'warning',
            'method' => 'deleteMovie',
            'id' => $id
        ]);
    }

    public function delete($id)
    {
        Movie::find($id)->delete();
        $this->dispatch('swal:modal', [
            'title' => '¡Eliminado!',
            'text' => 'La película ha sido eliminada con éxito.',
            'icon' => 'success'
        ]);
    }
}
