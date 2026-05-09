<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ReservationRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\RoomRepository;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $reservations = new ReservationRepository();

        $this->view('dashboard', [
            'title' => 'Dashboard',
            'stats' => $reservations->dashboardStats(),
            'latestReservations' => array_slice($reservations->all(), 0, 6),
            'rooms' => (new RoomRepository())->all(),
            'reviews' => array_slice((new ReviewRepository())->all(), 0, 4),
        ]);
    }
}

