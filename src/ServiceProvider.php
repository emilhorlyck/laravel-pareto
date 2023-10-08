<?php

namespace EmilHorlyck\LaravelPareto;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->register(ServiceProvider::class);

        dd('hej');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TestCommand::class
            ]);
        }

        dd('hej');
    }
}
