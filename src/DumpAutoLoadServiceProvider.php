<?php

namespace Staskjs\LaravelUtils;

use Illuminate\Support\ServiceProvider;

class DumpAutoLoadServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerArtisanCommands();
        }
    }

    protected function registerArtisanCommands()
    {
        $this->commands([
            Console\DumpAutoload::class,
        ]);
    }

}
