<?php namespace Staskjs\LaravelUtils\Middleware;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Illuminate\Support\Str;

class SqlLog
{
    protected $queryNumber = 0;

    protected $logger = null;

    public function __construct() {
        $this->logger = $this->getLogger();
    }

    public function handle($request, $next) {
        if (\Config::get('app.debug')) {
            \DB::enableQueryLog();
            $url = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
            $method = !empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
            if (!empty($url)) {
                $this->logger->info('');
                $date = \Carbon\Carbon::now();
                $this->logger->info("--------- $date --------- $method $url");
            }
            $this->logger->info('');
            \DB::listen(function($query) {
                $this->queryNumber++;

                $bindings = $query->bindings;
                foreach ($bindings as &$binding) {
                    $binding = is_numeric($binding) ? $binding : "'$binding'";
                }

                $sqlWords = [
                    'select', 'from', 'where', 'order by', 'group by', 'limit', 'offset', 'and', 'or', 'not',
                    'set', 'update', 'delete', 'insert', 'into', 'values', 'join', 'left', 'inner', 'outer', 'right', 'having',
                    'count', 'max', 'min', 'avg', 'sum', 'like', 'between', 'asc', 'desc', 'is',
                ];
                $sql = Str::replaceArray('?', $bindings, $query->sql);
                foreach ($sqlWords as $word) {
                    $sql = preg_replace("/\b{$word}\b/", strtoupper($word), $sql);
                }
                $sql = str_replace('`', '', $sql);
                $time = (string) $query->time;
                $maxTimeLength = 6;
                $numberOfSpaces = $maxTimeLength - strlen($time);

                $spaces = '';
                if ($numberOfSpaces > 0) {
                    $spaces = str_repeat(' ', $numberOfSpaces);
                }

                if ($this->queryNumber % 2 == 0) {
                    $this->logger->info("\033[38;5;12msql: ({$time} ms){$spaces}\033[0m {$sql};");
                }
                else {
                    $this->logger->info("\033[38;5;14msql: ({$time} ms){$spaces}\033[0m {$sql};");
                }
            });
        }

        return $next($request);
    }

    public function terminate($request, $response) {
        if (\Config::get('app.debug')) {
            $time = floor((microtime(true) - LARAVEL_START) * 1000);
            $queryLog = collect(\DB::getQueryLog());
            $queryCount = $queryLog->count();
            $queryTotalTime = $queryLog->sum(function($item) { return $item['time']; });
            $queryTotalTime = ceil($queryTotalTime);

            $code = $response->getStatusCode();
            $statusTexts = \Illuminate\Http\Response::$statusTexts;
            $status = isset($statusTexts[$code]) ? statusTexts[$code] : '';
            $status = "{$code} {$status}";

            $this->logger->info('');
            $this->logger->info("Completed {$status} in {$time} ms, database: ${queryTotalTime} ms ({$queryCount} queries)");
        }
    }

    protected function getLogger() {
        $logfile = $this->getLogFileName();
        $logStreamHandler = new StreamHandler(storage_path("logs/$logfile"), 'info');
        $logFormat = "%message%\n";
        $formatter = new LineFormatter($logFormat);
        $logStreamHandler->setFormatter($formatter);
        $logger = new Logger('sql');
        $logger->pushHandler($logStreamHandler);

        return $logger;
    }

    protected function getLogFileName() {
        if (env('APP_LOG') == 'daily') {
            $date = date('Y-m-d');
            return "laravel-$date.log";
        }

        return 'laravel.log';
    }
}
