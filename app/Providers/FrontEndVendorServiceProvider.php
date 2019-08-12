<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FrontEndVendorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        view()->composer('*',function($view){
            $js_vendors=config('frontend.js_vendor');

            $css_list=config('frontend.css_vendor');

            $js_vendors=array_map(function($d){
                return env('APP_RES').$d;
            },$js_vendors);

            $css_list=array_map(function($d){
                return env('APP_RES').$d;
            },$css_list);
            return $view->with(['js_vendors'=>$js_vendors,'css_vendors'=>$css_list]);
        });
    }
}
