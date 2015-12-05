<?php

namespace CatalogueMaker;

use Illuminate\Support\ServiceProvider;


class CatalogueMakerServiceProvider extends ServiceProvider
{
    public function register()
    {        
        $this->commands(['CatalogueMaker\Console\Commands\CatalogueMakerCommand']);
    }

    public function boot()
    {
        \Route::get('/testcatalogemaker', function () { echo 'CatalogueMakerServiceProvider: OK'; });
    }


}