<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ProductRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\RoomRepository;
use App\Services\ReservationService;

final class ReservationController extends Controller
{
    public function index(): void
    {
        $this->view('reservations/index', [
            'title' => 'Reservas',
            'reservations' => (new ReservationRepository())->all(),
        ]);
    }

    public function create(): void
    {
        $this->view('reservations/create', [
            'title' => 'Nova reserva',
            'roomTypes' => (new RoomRepository())->availableTypes(),
        ]);
    }

    public function store(): void
    {
        try {
            $id = (new ReservationService())->create($_POST);
            $this->redirect('/reservations/' . $id . '?success=' . urlencode('Reserva criada como pendente.'));
        } catch (\Throwable $exception) {
            $this->backWith('error', $exception->getMessage(), '/reservations/create');
        }
    }

    public function show(string $id): void
    {
        $repository = new ReservationRepository();
        $reservation = $repository->find((int) $id);

        if (!$reservation) {
            $this->backWith('error', 'Reserva nao encontrada.', '/reservations');
        }

        $service = new ReservationService();

        $this->view('reservations/show', [
            'title' => 'Reserva ' . $reservation['code'],
            'reservation' => $reservation,
            'consumptions' => $repository->consumptions((int) $id),
            'products' => (new ProductRepository())->available(),
            'total' => $service->calculateTotal((int) $id),
        ]);
    }

    public function confirm(string $id): void
    {
        $this->runAction(fn () => (new ReservationService())->confirm((int) $id, $_POST['method'] ?? 'PIX', $_POST['payment_status'] ?? 'Recusado'), $id);
    }

    public function cancel(string $id): void
    {
        $this->runAction(fn () => (new ReservationService())->cancel((int) $id, isset($_POST['maintenance'])), $id);
    }

    public function checkIn(string $id): void
    {
        $this->runAction(fn () => (new ReservationService())->checkIn((int) $id), $id);
    }

    public function noShow(string $id): void
    {
        $this->runAction(fn () => (new ReservationService())->markNoShow((int) $id), $id);
    }

    public function addConsumption(string $id): void
    {
        $this->runAction(
            fn () => (new ReservationService())->addConsumption((int) $id, (int) $_POST['product_id'], (int) $_POST['quantity']),
            $id
        );
    }

    public function checkOut(string $id): void
    {
        $this->runAction(fn () => (new ReservationService())->checkOut((int) $id, $_POST['method'] ?? 'PIX', $_POST['payment_status'] ?? 'Recusado'), $id);
    }

    private function runAction(callable $action, string $id): void
    {
        try {
            $action();
            $this->backWith('success', 'Operacao realizada.', '/reservations/' . $id);
        } catch (\Throwable $exception) {
            $this->backWith('error', $exception->getMessage(), '/reservations/' . $id);
        }
    }
}

