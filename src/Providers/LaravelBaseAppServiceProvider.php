<?php

declare(strict_types=1);

namespace DevBRLucas\Providers;

use DevBRLucas\Console\Commands\CreateInitialUser;
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
        ]);
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateInitialUser::class,
            ]);
        }
    }
}
