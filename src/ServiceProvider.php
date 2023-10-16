<?php

namespace EmilHorlyck\LaravelPareto;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use EmilHorlyck\LaravelPareto\Commands\InitCommand;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InitCommand::class
            ]);
        }
    }
}
