<?php

use App\Controllers\DashboardController;
use App\Controllers\ProductController;
use App\Controllers\ReservationController;
use App\Controllers\ReviewController;
use App\Controllers\RoomController;

$router->get('/', [DashboardController::class, 'index']);

$router->get('/rooms', [RoomController::class, 'index']);
$router->post('/rooms', [RoomController::class, 'store']);
$router->post('/rooms/{id}/status', [RoomController::class, 'updateStatus']);

$router->get('/products', [ProductController::class, 'index']);
$router->post('/products', [ProductController::class, 'store']);

$router->get('/reservations', [ReservationController::class, 'index']);
$router->get('/reservations/create', [ReservationController::class, 'create']);
$router->post('/reservations', [ReservationController::class, 'store']);
$router->get('/reservations/{id}', [ReservationController::class, 'show']);
$router->post('/reservations/{id}/confirm', [ReservationController::class, 'confirm']);
$router->post('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);
$router->post('/reservations/{id}/check-in', [ReservationController::class, 'checkIn']);
$router->post('/reservations/{id}/no-show', [ReservationController::class, 'noShow']);
$router->post('/reservations/{id}/consumptions', [ReservationController::class, 'addConsumption']);
$router->post('/reservations/{id}/check-out', [ReservationController::class, 'checkOut']);

$router->get('/reviews/create', [ReviewController::class, 'create']);
$router->post('/reviews', [ReviewController::class, 'store']);

