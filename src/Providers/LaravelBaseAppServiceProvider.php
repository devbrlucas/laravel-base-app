<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Providers;

use DevBRLucas\LaravelBaseApp\Console\Commands\CreateInitialUser;
use Illuminate\Support\ServiceProvider;

class LaravelBaseAppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/laravel-base-app.php' => config_path('laravel-base-app.php'),
        ], 'laravel-base-app.config');
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateInitialUser::class,
            ]);
            if (config('laravel-base-app.create_states_cities_tables')) {
                $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
            }
        }
    }
}
