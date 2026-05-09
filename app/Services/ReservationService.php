<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HotelStatus;
use App\Repositories\ProductRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\RoomRepository;
use DateTimeImmutable;
use InvalidArgumentException;

final class ReservationService
{
    public function __construct(
        private ?ReservationRepository $reservations = null,
        private ?RoomRepository $rooms = null,
        private ?ProductRepository $products = null,
    ) {
        $this->reservations ??= new ReservationRepository();
        $this->rooms ??= new RoomRepository();
        $this->products ??= new ProductRepository();
    }

    public function create(array $input): int
    {
        $this->requireFields($input, ['guest_name', 'email', 'phone', 'check_in', 'check_out', 'room_type', 'people']);

        if ($input['check_out'] <= $input['check_in']) {
            throw new InvalidArgumentException('A data de saida deve ser maior que a data de entrada.');
        }

        $people = (int) $input['people'];
        if ($people < 1) {
            throw new InvalidArgumentException('A quantidade de hospedes deve ser maior que zero.');
        }

        $room = $this->rooms->findAvailableForPeriod($input['room_type'], $people, $input['check_in'], $input['check_out']);

        if (!$room) {
            throw new InvalidArgumentException('Nao existe quarto disponivel para o tipo, periodo e quantidade informados.');
        }

        $guestId = $this->reservations->createGuest([
            'name' => $input['guest_name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
        ]);

        return $this->reservations->createReservation([
            'code' => $this->generateCode(),
            'guest_id' => $guestId,
            'room_id' => $room['id'],
            'check_in' => $input['check_in'],
            'check_out' => $input['check_out'],
            'people' => $people,
            'status' => HotelStatus::RESERVATION_PENDING,
            'reservation_fee' => round(((float) $room['daily_rate']) * 0.2, 2),
        ]);
    }

    public function confirm(int $reservationId, string $method, string $paymentStatus): void
    {
        $reservation = $this->reservationOrFail($reservationId);

        if ($reservation['status'] !== HotelStatus::RESERVATION_PENDING) {
            throw new InvalidArgumentException('Apenas reservas pendentes podem ser confirmadas por taxa.');
        }

        $approved = $paymentStatus === 'Aprovado';

        if ($approved && $this->reservations->hasConfirmedOverlap((int) $reservation['room_id'], $reservationId, $reservation['check_in'], $reservation['check_out'])) {
            throw new InvalidArgumentException('Este quarto ja possui reserva confirmada para o periodo informado.');
        }

        $this->reservations->createPayment([
            'reservation_id' => $reservationId,
            'type' => 'Taxa de reserva',
            'method' => $method,
            'amount' => $reservation['reservation_fee'],
            'status' => $paymentStatus,
        ]);

        $this->reservations->updateStatus(
            $reservationId,
            $approved ? HotelStatus::RESERVATION_CONFIRMED : HotelStatus::RESERVATION_CANCELLED
        );

        if ($approved) {
            $this->rooms->updateStatus((int) $reservation['room_id'], HotelStatus::ROOM_RESERVED);
        }
    }

    public function checkIn(int $reservationId): void
    {
        $reservation = $this->reservationOrFail($reservationId);

        if ($reservation['status'] !== HotelStatus::RESERVATION_CONFIRMED) {
            throw new InvalidArgumentException('Check-in permitido apenas para reservas confirmadas.');
        }

        if ($reservation['check_in'] !== date('Y-m-d')) {
            throw new InvalidArgumentException('O check-in so pode ser realizado na data de entrada da reserva.');
        }

        $this->reservations->updateStatus($reservationId, HotelStatus::RESERVATION_STAYING);
        $this->rooms->updateStatus((int) $reservation['room_id'], HotelStatus::ROOM_OCCUPIED);
    }

    public function markNoShow(int $reservationId): void
    {
        $reservation = $this->reservationOrFail($reservationId);
        $today = new DateTimeImmutable(date('Y-m-d'));
        $checkIn = new DateTimeImmutable($reservation['check_in']);

        if ($reservation['status'] !== HotelStatus::RESERVATION_CONFIRMED || $today <= $checkIn) {
            throw new InvalidArgumentException('Nao compareceu so pode ser marcado apos a data de entrada de uma reserva confirmada.');
        }

        $this->reservations->updateStatus($reservationId, HotelStatus::RESERVATION_NO_SHOW);
        $this->rooms->updateStatus((int) $reservation['room_id'], HotelStatus::ROOM_AVAILABLE);
    }

    public function cancel(int $reservationId, bool $maintenance = false): void
    {
        $reservation = $this->reservationOrFail($reservationId);

        if ($reservation['status'] === HotelStatus::RESERVATION_STAYING) {
            throw new InvalidArgumentException('Reserva em hospedagem deve passar pelo check-out.');
        }

        if (!in_array($reservation['status'], [HotelStatus::RESERVATION_PENDING, HotelStatus::RESERVATION_CONFIRMED], true)) {
            throw new InvalidArgumentException('Esta reserva nao pode mais ser cancelada.');
        }

        $this->reservations->updateStatus($reservationId, HotelStatus::RESERVATION_CANCELLED);
        $this->rooms->updateStatus(
            (int) $reservation['room_id'],
            $maintenance ? HotelStatus::ROOM_MAINTENANCE : HotelStatus::ROOM_AVAILABLE
        );
    }

    public function addConsumption(int $reservationId, int $productId, int $quantity): void
    {
        $reservation = $this->reservationOrFail($reservationId);

        if ($reservation['status'] !== HotelStatus::RESERVATION_STAYING) {
            throw new InvalidArgumentException('Consumos so podem ser registrados em reservas em hospedagem.');
        }

        if ($quantity < 1) {
            throw new InvalidArgumentException('A quantidade deve ser maior que zero.');
        }

        $product = $this->products->find($productId);
        if (!$product || (int) $product['available'] !== 1) {
            throw new InvalidArgumentException('Produto ou servico indisponivel.');
        }

        $this->reservations->addConsumption([
            'reservation_id' => $reservationId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price' => $product['price'],
        ]);
    }

    public function checkOut(int $reservationId, string $method, string $paymentStatus): float
    {
        $reservation = $this->reservationOrFail($reservationId);

        if ($reservation['status'] !== HotelStatus::RESERVATION_STAYING) {
            throw new InvalidArgumentException('Check-out permitido apenas para reservas em hospedagem.');
        }

        $total = $this->calculateTotal($reservationId);
        $approved = $paymentStatus === 'Aprovado';

        $this->reservations->createPayment([
            'reservation_id' => $reservationId,
            'type' => 'Pagamento final',
            'method' => $method,
            'amount' => $total,
            'status' => $paymentStatus,
        ]);

        if ($approved) {
            $this->reservations->updateStatus($reservationId, HotelStatus::RESERVATION_FINISHED);
            $this->rooms->updateStatus((int) $reservation['room_id'], HotelStatus::ROOM_CLEANING);
        }

        return $total;
    }

    public function calculateTotal(int $reservationId): float
    {
        $reservation = $this->reservationOrFail($reservationId);
        $days = max(1, (new DateTimeImmutable($reservation['check_out']))->diff(new DateTimeImmutable($reservation['check_in']))->days);
        $hostingTotal = $days * (float) $reservation['daily_rate'];
        return $hostingTotal + $this->reservations->consumptionTotal($reservationId);
    }

    private function reservationOrFail(int $reservationId): array
    {
        $reservation = $this->reservations->find($reservationId);

        if (!$reservation) {
            throw new InvalidArgumentException('Reserva nao encontrada.');
        }

        return $reservation;
    }

    private function requireFields(array $input, array $fields): void
    {
        foreach ($fields as $field) {
            if (!isset($input[$field]) || trim((string) $input[$field]) === '') {
                throw new InvalidArgumentException('Preencha todos os campos obrigatorios.');
            }
        }
    }

    private function generateCode(): string
    {
        do {
            $code = 'HM-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        } while ($this->reservations->findByCode($code));

        return $code;
    }
}
