<?php

namespace App\Providers;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
public function boot()
{
    Broadcast::routes(['middleware' => ['auth:sanctum']]); // or 'auth:api' if using token
    require base_path('routes/channels.php');
}

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}