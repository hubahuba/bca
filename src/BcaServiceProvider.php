<?php

namespace Ngungut\Bca;

use Illuminate\Support\ServiceProvider;
use Ngungut\Bca\Console\BcaInit;
use Ngungut\Bca\Console\BcaSandbox;

class BcaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/bca.php', 'bca');
        $this->mergeConfigFrom(__DIR__ . '/../config/filesystems.php', 'filesystems');
        $this->mergeConfigFrom(__DIR__ . '/../config/logging.php', 'logging');

        $this->publishes(array(
            __DIR__ . '/../config/bca.php' => config_path('bca.php'),
        ));
        $this->publishes(array(
            __DIR__ . '/../config/filesystems.php' => config_path('filesystems.php'),
        ));
        $this->publishes(array(
            __DIR__ . '/../config/logging.php' => config_path('logging.php'),
        ));

        $this->commands('ngungut.bca.init');
        $this->commands('ngungut.bca.sandbox');
    }

    public function register()
    {
        $this->registerBCACommand();
    }

    /**
     * Register the Artisan command.
     *
     * @return void
     */
    protected function registerBCACommand()
    {
        $this->app->singleton('ngungut.bca.init', function () {
            return new BcaInit;
        });

        $this->app->singleton('ngungut.bca.sandbox', function () {
            return new BcaSandbox;
        });
    }
}