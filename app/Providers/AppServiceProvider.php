<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

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
        // កូដនេះនឹងបញ្ជូនទិន្នន័យទៅគ្រប់ View ដែលមានប្រើប្រាស់ Navbar
        View::composer('*', function ($view) {
            $view->with('navbarCategories', Category::all());
        });
    }
}
