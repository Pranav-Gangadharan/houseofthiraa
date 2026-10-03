<?php

namespace App\Providers;

use App\Support\Cart;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        View::composer('layouts.shop', function ($view) {
            $view->with('bagCount', app(Cart::class)->count());
        });
    }
}
