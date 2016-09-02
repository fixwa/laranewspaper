<?php

namespace App\Providers;

use App\Helpers\ArticleHelper;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('articleHelper', function ($app) {
            return new ArticleHelper;
        });
    }
}
