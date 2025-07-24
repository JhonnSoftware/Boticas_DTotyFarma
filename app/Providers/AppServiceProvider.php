<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Alertas;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $alertas = Alertas::orderBy('created_at', 'desc')->take(5)->get(); // últimos 5
            $cantidad = Alertas::where('leido', false)->count();

            $view->with(compact('alertas', 'cantidad'));
        });
    }
}
