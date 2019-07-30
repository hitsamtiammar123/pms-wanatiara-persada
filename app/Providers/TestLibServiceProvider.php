<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\TestLib;

class TestLibServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $d=new TestLib();
        $this->app->instance('App\Library\TestLib',$d);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
