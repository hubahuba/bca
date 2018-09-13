<?php

namespace Ngungut\Bca;

use Illuminate\Support\ServiceProvider;

class BcaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/bca.php', 'bca');
        $this->publishes(array(
            __DIR__ . '/../config/bca.php' => config_path('bca.php'),
        ));
    }

    public function register()
    {

    }
}