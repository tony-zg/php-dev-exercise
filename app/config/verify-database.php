<?php
// Run from the application directory: php config/verify-database.php
require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/bootstrap.php';
$connection = \Cake\Datasource\ConnectionManager::get('default');
$result = $connection->execute('SELECT 1 AS connected, VERSION() AS version')->fetch('assoc');
if ((int)$result['connected'] !== 1) {
    throw new RuntimeException('Database check failed');
}
printf("CakePHP database connection OK (MySQL %s)\n", $result['version']);
