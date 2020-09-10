<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
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
        // Using class based composers...
        View::composer(
            ['layouts.*', 'buyer.auth.login', 'pages.catalog_category', 'pages.category','pages.sub_category','pages.home'], 'App\Http\ViewComposers\MainLayout'
        );

        View::composer(
            'admin.layouts.main', 'App\Http\ViewComposers\AdminLayout'
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
