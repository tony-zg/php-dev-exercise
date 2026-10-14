<?php
declare(strict_types=1);

use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Sqlite;
use function Cake\Core\env;

return [
    'debug' => filter_var(env('DEBUG', 'true'), FILTER_VALIDATE_BOOLEAN),
    'Security' => ['salt' => env('SECURITY_SALT')],
    'Datasources' => [
        'default' => [
            'driver' => Mysql::class,
            'host' => env('DB_HOST', 'db'),
            'port' => 3306,
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'database' => env('DB_DATABASE'),
            'encoding' => 'utf8mb4',
            'url' => null,
        ],
        'test' => [
            'driver' => Sqlite::class,
            'database' => TMP . 'tests.sqlite',
            'url' => null,
        ],
    ],
];
