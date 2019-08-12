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
                return res_url($d);
            },$js_vendors);

            $css_list=array_map(function($d){
                return res_url($d);
            },$css_list);
            return $view->with(['js_vendors'=>collect($js_vendors),'css_vendors'=>collect($css_list)]);
        });
    }
}
