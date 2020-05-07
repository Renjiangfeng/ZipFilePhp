<?php

namespace Eric\ZipFilePhp;

use Eric\ZipFilePhp\Commands\ZipFilesFilterIgnore;
use Eric\ZipFilePhp\Commands\ZipFilesForConfig;
use Illuminate\Support\ServiceProvider;

class ZipFilePhpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/zip-file-php.php', 'zip-file-php'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/zip-file-php.php' => config_path('zip-file-php.php'),
        ]);
        if ($this->app->runningInConsole()) {
            $this->commands([
                ZipFilesForConfig::class,
                ZipFilesFilterIgnore::class,
            ]);
        }
    }
}
