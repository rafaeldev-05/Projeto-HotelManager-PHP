<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/Core/Autoloader.php';

use App\Core\Autoloader;
use App\Core\JsonStore;
use App\Models\HotelStatus;

Autoloader::register(dirname(__DIR__));

$config = require dirname(__DIR__) . '/config/app.php';
JsonStore::configure($config['database_path']);

$now = date('Y-m-d H:i:s');

JsonStore::reset([
    'guests' => [],
    'rooms' => [
        ['id' => 1, 'number' => '101', 'type' => 'Standard', 'capacity' => 2, 'daily_rate' => 220.00, 'status' => HotelStatus::ROOM_AVAILABLE, 'created_at' => $now],
        ['id' => 2, 'number' => '102', 'type' => 'Standard', 'capacity' => 2, 'daily_rate' => 220.00, 'status' => HotelStatus::ROOM_AVAILABLE, 'created_at' => $now],
        ['id' => 3, 'number' => '201', 'type' => 'Luxo', 'capacity' => 3, 'daily_rate' => 380.00, 'status' => HotelStatus::ROOM_AVAILABLE, 'created_at' => $now],
        ['id' => 4, 'number' => '202', 'type' => 'Luxo', 'capacity' => 3, 'daily_rate' => 420.00, 'status' => HotelStatus::ROOM_CLEANING, 'created_at' => $now],
        ['id' => 5, 'number' => '301', 'type' => 'Familia', 'capacity' => 5, 'daily_rate' => 560.00, 'status' => HotelStatus::ROOM_AVAILABLE, 'created_at' => $now],
        ['id' => 6, 'number' => '401', 'type' => 'Suite Master', 'capacity' => 2, 'daily_rate' => 790.00, 'status' => HotelStatus::ROOM_MAINTENANCE, 'created_at' => $now],
    ],
    'products' => [
        ['id' => 1, 'name' => 'Agua mineral', 'description' => 'Garrafa 500ml', 'price' => 6.00, 'available' => 1, 'created_at' => $now],
        ['id' => 2, 'name' => 'Refrigerante', 'description' => 'Lata 350ml', 'price' => 9.00, 'available' => 1, 'created_at' => $now],
        ['id' => 3, 'name' => 'Lavanderia', 'description' => 'Servico por peca', 'price' => 18.00, 'available' => 1, 'created_at' => $now],
        ['id' => 4, 'name' => 'Cafe da manha extra', 'description' => 'Cortesia adicional fora da diaria', 'price' => 35.00, 'available' => 1, 'created_at' => $now],
        ['id' => 5, 'name' => 'Estacionamento', 'description' => 'Diaria de estacionamento', 'price' => 40.00, 'available' => 1, 'created_at' => $now],
    ],
    'reservations' => [],
    'consumptions' => [],
    'payments' => [],
    'reviews' => [],
]);

echo "Banco JSON criado em {$config['database_path']}" . PHP_EOL;

