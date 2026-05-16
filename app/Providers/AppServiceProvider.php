<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // if (app()->environment('local')) {
        //     $this->app->bind(\GuzzleHttp\Client::class, function () {
        //         return new \GuzzleHttp\Client([
        //             'verify' => false,
        //         ]);
        //     });
        // }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
