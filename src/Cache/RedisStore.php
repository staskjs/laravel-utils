<?php namespace Staskjs\LaravelUtils\Cache;

use Illuminate\Cache\RedisStore as LaravelRedisStore;

class RedisStore extends LaravelRedisStore
{
    /**
     * Begin executing a new tags operation.
     *
     * @param  array|mixed  $names
     * @return \Illuminate\Cache\RedisTaggedCache
     */
    public function tags($names)
    {
        $tags = config('cache.default_tags', [config('app.name') . ' ' . config('app.env')]);
        $tags = array_merge($tags, $names);
        return parent::tags($tags);
    }
}
