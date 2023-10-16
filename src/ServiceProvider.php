<?php

namespace EmilHorlyck\LaravelPareto;

use EmilHorlyck\LaravelPareto\Commands\InitCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

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
                InitCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/Resources/ArchTest.php' => config_path('../tests/Feature/Pareto/ArchTest.php'),
        ], 'Laravel-pareto-tests');
    }
}
