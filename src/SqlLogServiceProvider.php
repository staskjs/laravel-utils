<?php

namespace Staskjs\LaravelUtils;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Illuminate\Support\Str;

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
            $logger = $this->getLogger();
            $url = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
            $method = !empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
            if (!empty($url)) {
                $logger->info('');
                $date = \Carbon\Carbon::now();
                $logger->info("--------- $date --------- $method $url");
            }
            $logger->info('');
            \DB::listen(function($query) use ($logger) {
                $bindings = $query->bindings;
                foreach ($bindings as &$binding) {
                    $binding = is_numeric($binding) ? $binding : "'$binding'";
                }

                $sqlWords = [
                    'select', 'from', 'where', 'order by', 'group by', 'limit', 'offset', 'and', 'or', 'not',
                    'set', 'update', 'delete', 'insert', 'into', 'values', 'join', 'left', 'inner', 'outer', 'right', 'having',
                    'count', 'max', 'min', 'avg', 'sum', 'like', 'between', 'asc', 'desc',
                ];
                $sql = Str::replaceArray('?', $bindings, $query->sql);
                foreach ($sqlWords as $word) {
                    $sql = preg_replace("/\b{$word}\b/", strtoupper($word), $sql);
                }
                $sql = str_replace('`', '', $sql);
                $logger->info("sql: ({$query->time} ms) {$sql};");
            });
        }
    }

    protected function getLogger() {
        $logStreamHandler = new StreamHandler(storage_path('logs/laravel.log'), 'info');
        $logFormat = "%message%\n";
        $formatter = new LineFormatter($logFormat);
        $logStreamHandler->setFormatter($formatter);
        $logger = new Logger('sql');
        $logger->pushHandler($logStreamHandler);

        return $logger;
    }
}
