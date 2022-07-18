<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class KeycloakServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        $jdbcParsing = array();
        preg_match('/jdbc:postgresql:\/\/(.*):(.*)\/(.*)/', env('SPRING_DATASOURCE_URL'), $jdbcParsing);

       if(env('SPRING_DATASOURCE_URL')):

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
       endif;
    }
}