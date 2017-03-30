<?php

namespace Staskjs\LaravelUtils\Testing;

trait DatabaseTruncate
{
    public function truncateDatabase()
    {
        $dbname = env('DB_DATABASE');

        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = collect(\DB::select('SHOW TABLES'))->map(function($value) use ($dbname) {
            return $value->{"Tables_in_$dbname"};
        })->each(function($table) {
            if ($table != 'migrations') {
                \DB::table($table)->truncate();
            }
        });
    }
}
