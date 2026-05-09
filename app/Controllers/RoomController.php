<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\HotelStatus;
use App\Repositories\RoomRepository;

final class RoomController extends Controller
{
    public function index(): void
    {
        $this->view('rooms/index', [
            'title' => 'Quartos',
            'rooms' => (new RoomRepository())->all(),
            'statuses' => HotelStatus::roomStatuses(),
        ]);
    }

    public function store(): void
    {
        try {
            (new RoomRepository())->create([
                'number' => trim($_POST['number'] ?? ''),
                'type' => trim($_POST['type'] ?? ''),
                'capacity' => (int) ($_POST['capacity'] ?? 0),
                'daily_rate' => (float) ($_POST['daily_rate'] ?? 0),
                'status' => $_POST['status'] ?? HotelStatus::ROOM_AVAILABLE,
            ]);

            $this->backWith('success', 'Quarto cadastrado.', '/rooms');
        } catch (\Throwable $exception) {
            $this->backWith('error', $exception->getMessage(), '/rooms');
        }
    }

    public function updateStatus(string $id): void
    {
        (new RoomRepository())->updateStatus((int) $id, $_POST['status'] ?? HotelStatus::ROOM_AVAILABLE);
        $this->backWith('success', 'Status do quarto atualizado.', '/rooms');
    }
}

