<?php

namespace Staskjs\LaravelUtils\Testing;

trait DatabaseTruncate
{
    public function truncateDatabase()
    {
        $connection = config('database.default');
        $dbname = config("database.connections.{$connection}.database");

        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = collect(\DB::select("SHOW FULL TABLES WHERE Table_Type != 'VIEW'"))->map(function($value) use ($dbname) {
            return $value->{"Tables_in_$dbname"};
        })->each(function($table) {
            if ($table != 'migrations') {
                \DB::table($table)->truncate();
            }
        });
    }
}
