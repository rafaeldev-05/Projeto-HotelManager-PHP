<?php

$basePath = dirname(__DIR__);
$localDatabasePath = $basePath . '/storage/hotelmanager.json';
$databasePath = $localDatabasePath;

if (getenv('VERCEL')) {
    $databasePath = sys_get_temp_dir() . '/hotelmanager.json';

    if (!is_file($databasePath) && is_file($localDatabasePath)) {
        copy($localDatabasePath, $databasePath);
    }
}

return [
    'name' => 'HotelManager',
    'base_path' => $basePath,
    'database_path' => $databasePath,
];
