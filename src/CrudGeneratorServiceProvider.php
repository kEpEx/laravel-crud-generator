<?php

namespace CrudGenerator;

use Illuminate\Support\ServiceProvider;


class CrudGeneratorServiceProvider extends ServiceProvider
{
    public function register()
    {        
        $this->commands(['CrudGenerator\Console\Commands\CrudGeneratorCommand']);
    }

    public function boot()
    {
        \Route::get('/testcrudgenerator', function () { echo 'CrudGeneratorServiceProvider: OK'; });
        $this->loadViewsFrom(__DIR__.'/views', 'crudgenerator');

        $this->publishes([
	        __DIR__.'/Templates' => base_path('resources/templates'),
	    ]);
    }


}