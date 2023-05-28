<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(\App\Repositories\CalificacionRepository::class, \App\Repositories\CalificacionService::class);
        $this->app->bind(\App\Repositories\ComentarioRepository::class, \App\Repositories\ComentarioService::class);
        $this->app->bind(\App\Repositories\VisualizacionRepository::class, \App\Repositories\VisualizacionService::class);
        $this->app->bind(\App\Repositories\ResponseApiRepository::class, \App\Repositories\ResponseApiService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
