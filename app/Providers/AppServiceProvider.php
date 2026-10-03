<?php

namespace App\Providers;

use App\Support\Cart;
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
        View::composer('layouts.shop', function ($view) {
            $view->with('bagCount', app(Cart::class)->count());
        });
    }
}
