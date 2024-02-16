<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config([
            'logging.channels.single.path' => \Phar::running()
                ? dirname(\Phar::running(false)) . '/logs/' . date('Y-m-d') . '-syn-log.log'
                : storage_path('logs/laravel.log'),

            'app.setting_file' => \Phar::running()
                ? dirname(\Phar::running(false)) . '/appsettings.json'
                : __DIR__  . '/../../appsettings.json',
        ]);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
