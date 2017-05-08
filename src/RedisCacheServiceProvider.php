<?php

namespace Staskjs\LaravelUtils;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Arr;

class RedisCacheServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        \Cache::extend('redis', function ($app) {
            $config = $this->app['config']['cache.stores.redis'];

            $redis = $this->app['redis'];

            $connection = Arr::get($config, 'connection', 'default');

            return \Cache::repository(new Cache\RedisStore($redis, $this->getPrefix($config), $connection));
        });
    }

}
