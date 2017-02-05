<?php

namespace LArtie\MagtuTelegramBot;

use Illuminate\Support\ServiceProvider;
use LArtie\MagtuTelegramBot\Controllers\TelegramBotController;

class MagtuTelegramBotServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__ . '/../routes.php';

        $this->app->make(TelegramBotController::class);
    }
}
