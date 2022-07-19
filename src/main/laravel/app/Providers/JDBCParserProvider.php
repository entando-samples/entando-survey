<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\ServiceProvider;

class JDBCParserProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $jdbcParsing = array();
        preg_match('/jdbc:postgresql:\/\/(.*):(.*)\/(.*)/', env('SPRING_DATASOURCE_URL'), $jdbcParsing);
        Log::debug([
                'database.entandopgsql.driver' => 'pgsql',
                'database.entandopgsql.host' => $jdbcParsing[1],
                'database.entandopgsql.port' => $jdbcParsing[2],
                'database.entandopgsql.database' => $jdbcParsing[3],
                'database.entandopgsql.username' => env('SPRING_DATASOURCE_USERNAME', 'forge'),
                'database.entandopgsql.password' => env('SPRING_DATASOURCE_PASSWORD', ''),
                'database.entandopgsql.charset' => 'utf8',
                'database.entandopgsql.prefix' => '',
                'database.entandopgsql.prefix_indexes' => true,
                'database.entandopgsql.schema' => 'public',
                'database.entandopgsql.sslmode' => 'prefer',
        ]);

        if(env('SPRING_DATASOURCE_URL')) {
            config([
                'database.entandopgsql.driver' => 'pgsql',
                'database.entandopgsql.url' => env('DATABASE_URL'),
                'database.entandopgsql.host' => $jdbcParsing[1],
                'database.entandopgsql.port' => $jdbcParsing[2],
                'database.entandopgsql.database' => $jdbcParsing[3],
                'database.entandopgsql.username' => env('SPRING_DATASOURCE_USERNAME', 'forge'),
                'database.entandopgsql.password' => env('SPRING_DATASOURCE_PASSWORD', ''),
                'database.entandopgsql.charset' => 'utf8',
                'database.entandopgsql.prefix' => '',
                'database.entandopgsql.prefix_indexes' => true,
                'database.entandopgsql.schema' => 'public',
                'database.entandopgsql.sslmode' => 'prefer',
            ]);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            Log::critical("Could not connect to the database.  Please check your configuration. error: " . $e );
        }
    }
}
