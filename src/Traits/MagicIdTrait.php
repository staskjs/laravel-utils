<?php namespace Staskjs\LaravelUtils\Traits;

trait MagicIdTrait
{
    protected static function bootMagicIdTrait()
    {
        self::creating(function($model) {
            $microtime = microtime(true);
            $milliseconds = sprintf("%03d", ($microtime - floor($microtime)) * 1000);
            $rand = rand(10, 99);
            if (empty($model->id)) {
                $model->id = date('ymdHis'. $milliseconds . $rand, $microtime);
            }
        });
    }
}
