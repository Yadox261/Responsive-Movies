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
    public $is_premiere = false;

    // Campos temporales para crear nuevos horarios con valores predeterminados
    public $new_day = 'Todos los días';
    public $new_time = '18:00';
    public $new_room = 'Sala 1';
    public $new_format = '2D Español';

    // Propiedad para el visualizador de salas
    public $viewDay = 'Todos los días';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updated($propertyName)
    {
        // Solo validar en tiempo real los campos de la película principal
        if (in_array($propertyName, ['title', 'director', 'release_year', 'genre', 'synopsis', 'poster', 'banner'])) {
            $this->validateOnly($propertyName, [
                'title' => 'required',
                'director' => 'required',
                'release_year' => 'required|numeric',
                'genre' => 'required',
                'synopsis' => 'required',
                'poster' => 'nullable|image|max:2048',
                'banner' => 'nullable|image|max:2048',
            ]);
        }
    }

    public function changeViewDay($day)
    {
        $this->viewDay = $day;
    }

    private function parseDurationToMinutes($duration)
    {
        if (empty($duration)) {
            return 120; // Default to 2 hours
        }
        $duration = strtolower($duration);
        $hours = 0;
        $minutes = 0;
        if (str_contains($duration, 'h')) {
            $parts = explode('h', $duration);
            $hours = (int)trim($parts[0]);
            if (isset($parts[1])) {
                $minutesPart = trim($parts[1]);
                $minutes = (int)filter_var($minutesPart, FILTER_SANITIZE_NUMBER_INT);
            }
        } else {
            $minutes = (int)filter_var($duration, FILTER_SANITIZE_NUMBER_INT);
        }
        $total = ($hours * 60) + $minutes;
        return $total > 0 ? $total : 120;
    }

    private function getDaysArray($dayGroup)
    {
        switch ($dayGroup) {
            case 'Todos los días':
                return ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            case 'Lunes a Viernes':
                return ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
            case 'Fin de Semana':
                return ['Sábado', 'Domingo'];
            default:
                return [$dayGroup];
        }
    }

    private function daysOverlap($day1, $day2)
    {
        $days1 = $this->getDaysArray($day1);
        $days2 = $this->getDaysArray($day2);
        return count(array_intersect($days1, $days2)) > 0;
    }

    public function addSchedule()
    {
        $this->validate([
            'new_day' => 'required|string',
            'new_time' => 'required|string',
            'new_room' => 'required|string',
            'new_format' => 'required|string',
        ], [
            'new_day.required' => 'El día es obligatorio.',
            'new_time.required' => 'La hora es obligatoria.',
            'new_room.required' => 'La sala es obligatoria.',
            'new_format.required' => 'El formato es obligatorio.',
        ]);

        // 1. Validar límite de horario de inicio
        // 11:00 AM = 660 mins, 9:00 PM = 1260 mins, 11:00 PM = 1380 mins
        list($hours, $mins) = explode(':', $this->new_time);
        $newStart = ($hours * 60) + $mins;

        if ($newStart < 660) {
            $this->addError('new_time', 'El horario de inicio no puede ser anterior a las 11:00 AM (11:00).');
            return;
        }

        $maxStart = $this->is_premiere ? 1380 : 1260;
        if ($newStart > $maxStart) {
            $limitStr = $this->is_premiere ? '11:00 PM (23:00)' : '9:00 PM (21:00)';
            $errorMsg = $this->is_premiere 
                ? "Para estrenos, el último horario de inicio permitido es a las {$limitStr}."
                : "Para películas regulares, el último horario de inicio permitido es a las {$limitStr}. ¡Marca la película como Estreno si deseas habilitar funciones hasta las 11:00 PM!";
            $this->addError('new_time', $errorMsg);
            return;
        }

        // 2. Calcular colisiones con buffer de 15 minutos
        $durationInMinutes = $this->parseDurationToMinutes($this->duration);
        $newEnd = $newStart + $durationInMinutes + 15;

        $existingSchedules = \App\Models\Schedule::where('room', $this->new_room)
            ->with('movie')
            ->get();

        foreach ($existingSchedules as $existing) {
            if ($this->daysOverlap($this->new_day, $existing->day)) {
                $exDuration = $this->parseDurationToMinutes($existing->movie?->duration);
                list($exHours, $exMins) = explode(':', $existing->time);
                $exStart = ($exHours * 60) + $exMins;
                $exEnd = $exStart + $exDuration + 15;

                if ($newStart < $exEnd && $exStart < $newEnd) {
                    $exStartStr = $existing->time;
                    $exEndHour = floor(($exStart + $exDuration) / 60);
                    $exEndMin = ($exStart + $exDuration) % 60;
                    $exEndStr = sprintf('%02d:%02d', $exEndHour, $exEndMin);
                    
                    $conflictMsg = "¡Conflicto de Sala! La {$this->new_room} ya está ocupada por la película \"{$existing->movie->title}\" de {$exStartStr} a {$exEndStr} (más 15 min de limpieza) el día \"{$existing->day}\".";
                    $this->addError('new_time', $conflictMsg);
                    return;
                }
            }
        }

        \App\Models\Schedule::create([
            'movie_id' => $this->movie_id,
            'day' => $this->new_day,
            'time' => $this->new_time,
            'room' => $this->new_room,
            'format' => $this->new_format,
        ]);

        $this->new_day = 'Todos los días';
        $this->new_time = '18:00';
        $this->new_room = 'Sala 1';
        $this->new_format = '2D Español';
        session()->flash('schedule_success', 'Horario agregado con éxito.');
    }

    public function deleteSchedule($id)
    {
        $schedule = \App\Models\Schedule::find($id);
        if ($schedule) {
            $schedule->delete();
        }
        session()->flash('schedule_success', 'Horario eliminado con éxito.');
    }

    public function mount(?Movie $movie = null)
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
            $this->is_premiere = (bool) $movie->is_premiere;
        }
    }

    public function render()
    {
        $schedules = [];
        if ($this->movie_id) {
            $schedules = \App\Models\Schedule::where('movie_id', $this->movie_id)->latest()->get();
        }

        // Preparar datos de la línea de tiempo
        $rooms = ['Sala 1', 'Sala 2', 'Sala 3', 'Sala 4', 'Sala 5', 'Sala VIP', 'Sala 3D', 'Sala IMAX'];
        $timelineSchedules = \App\Models\Schedule::with('movie')
            ->where('day', $this->viewDay)
            ->get()
            ->groupBy('room');

        $timelineData = [];
        foreach ($rooms as $room) {
            $roomSchedules = $timelineSchedules->get($room, collect());
            $formattedSchedules = [];
            foreach ($roomSchedules as $sched) {
                $dur = $this->parseDurationToMinutes($sched->movie?->duration);
                list($h, $m) = explode(':', $sched->time);
                $start = ($h * 60) + $m;
                
                // Porcentaje relativo a 11:00 AM (660 mins) a 12:00 AM (1440 mins)
                $timelineStart = 660;
                $timelineEnd = 1440;
                $timelineTotal = $timelineEnd - $timelineStart; // 780 mins
                
                $left = (($start - $timelineStart) / $timelineTotal) * 100;
                $width = ($dur / $timelineTotal) * 100;
                $bufferWidth = (15 / $timelineTotal) * 100;
                
                // Clamping
                $left = max(0, min(100, $left));
                $width = max(0, min(100 - $left, $width));
                $bufferWidth = max(0, min(100 - $left - $width, $bufferWidth));
                
                $endTimeMins = $start + $dur;
                $endH = floor($endTimeMins / 60);
                $endM = $endTimeMins % 60;
                $endTimeStr = sprintf('%02d:%02d', $endH, $endM);
                
                $formattedSchedules[] = [
                    'id' => $sched->id,
                    'movie_title' => $sched->movie?->title ?? 'Sin Título',
                    'time_start' => $sched->time,
                    'time_end' => $endTimeStr,
                    'format' => $sched->format,
                    'left' => $left,
                    'width' => $width,
                    'buffer_left' => $left + $width,
                    'buffer_width' => $bufferWidth,
                    'is_current_movie' => ($sched->movie_id == $this->movie_id),
                ];
            }
            $timelineData[$room] = $formattedSchedules;
        }

        return view('livewire.movie-editor', [
            'schedules' => $schedules,
            'timelineData' => $timelineData,
            'rooms' => $rooms,
        ])->layout('layouts.app');
    }

    public function save()
    {
        // Usamos un bloque try-catch para capturar los errores de validación
        // y poder redirigir al usuario automáticamente a la pestaña correspondiente.
        try {
            $this->validate([
                'title' => 'required',
                'director' => 'required',
                'release_year' => 'required|numeric',
                'genre' => 'required',
                'synopsis' => 'required',
                'poster' => 'nullable|image|max:2048',
                'banner' => 'nullable|image|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            
            // Si hay errores en los campos de la sección General, activamos esa pestaña
            if ($errors->hasAny(['title', 'genre', 'release_year', 'synopsis'])) {
                $this->activeTab = 'general';
            }
            // Si no, si hay errores en la sección Multimedia, activamos esa pestaña
            elseif ($errors->hasAny(['poster', 'banner'])) {
                $this->activeTab = 'multimedia';
            }
            // Si no, si hay errores en la sección de Reparto, activamos esa pestaña
            elseif ($errors->hasAny(['director'])) {
                $this->activeTab = 'reparto';
            }

            // Despachamos un evento al navegador para que el script de JS haga scroll automático
            $this->dispatch('validation-failed');

            // Volvemos a lanzar la excepción para que Livewire pinte los errores en la vista
            throw $e;
        }

        $data = [
            'title' => $this->title,
            'director' => $this->director,
            'cast' => $this->cast,
            'release_year' => $this->release_year,
            'genre' => $this->genre,
            'duration' => $this->duration,
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

        session()->flash('swal', [
            'title' => $this->isEdit ? '¡Actualizado!' : '¡Creado!',
            'text' => 'Película guardada con éxito.',
            'icon' => 'success'
        ]);

        return redirect()->route('movies.index');
    }
}
