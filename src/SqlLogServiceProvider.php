<?php

namespace Dq\LaravelUtils;

use Illuminate\Support\ServiceProvider;

class SqlLogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (\Config::get('app.debug')) {
            $url = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
            \Log::info("-------------------");
            if (!empty($url)) {
                \Log::info("--------- $url");
            }
            \Log::info("-------------------");
            \DB::listen(function($query) {
                \Log::info("({$query->time} ms) {$query->sql}");
            });
        }
    }
}
