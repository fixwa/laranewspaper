<?php

namespace App\Providers;

use App\Http\ViewComposers\HeaderComposer;
use App\Http\ViewComposers\SidebarComposer;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(
            ['layouts.frontend'],
            HeaderComposer::class
        );
        view()->composer(
            ['frontend._sidebar'],
            SidebarComposer::class
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
