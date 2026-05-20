<?php

namespace App\Providers;

use App\Models\Schedule;
use App\Observers\ScheduleObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * Se registra el observer del modelo Schedule para enviar notificaciones
     * automáticas (correo + PDF + WhatsApp) al crear nuevos horarios.
     */
    public function boot(): void
    {
        Schedule::observe(ScheduleObserver::class);
    }
}
