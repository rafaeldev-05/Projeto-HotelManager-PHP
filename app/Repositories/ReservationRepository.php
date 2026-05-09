<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\JsonStore;

final class ReservationRepository
{
    public function all(): array
    {
        $reservations = array_map(fn (array $reservation): array => $this->withRelations($reservation), JsonStore::all('reservations'));
        usort($reservations, fn (array $a, array $b): int => strcmp($b['created_at'], $a['created_at']));
        return $reservations;
    }

    public function find(int $id): ?array
    {
        $reservation = JsonStore::find('reservations', $id);
        return $reservation ? $this->withRelations($reservation) : null;
    }

    public function findByCode(string $code): ?array
    {
        $matches = JsonStore::where('reservations', fn (array $reservation): bool => $reservation['code'] === $code);
        return $matches[0] ?? null;
    }

    public function createGuest(array $data): int
    {
        return JsonStore::insert('guests', $data);
    }

    public function createReservation(array $data): int
    {
        return JsonStore::insert('reservations', $data);
    }

    public function updateStatus(int $reservationId, string $status): void
    {
        JsonStore::update('reservations', $reservationId, ['status' => $status]);
    }

    public function createPayment(array $data): void
    {
        JsonStore::insert('payments', $data);
    }

    public function addConsumption(array $data): void
    {
        JsonStore::insert('consumptions', $data);
    }

    public function consumptions(int $reservationId): array
    {
        $items = JsonStore::where('consumptions', fn (array $item): bool => (int) $item['reservation_id'] === $reservationId);

        return array_map(function (array $item): array {
            $product = JsonStore::find('products', (int) $item['product_id']);
            $item['name'] = $product['name'] ?? 'Item removido';
            return $item;
        }, $items);
    }

    public function consumptionTotal(int $reservationId): float
    {
        return array_reduce(
            $this->consumptions($reservationId),
            fn (float $total, array $item): float => $total + ((int) $item['quantity'] * (float) $item['unit_price']),
            0.0
        );
    }

    public function hasConfirmedOverlap(int $roomId, int $ignoreReservationId, string $checkIn, string $checkOut): bool
    {
        foreach (JsonStore::all('reservations') as $reservation) {
            $sameRoom = (int) $reservation['room_id'] === $roomId;
            $sameReservation = (int) $reservation['id'] === $ignoreReservationId;
            $active = in_array($reservation['status'], ['Confirmada', 'Em hospedagem'], true);
            $overlaps = $reservation['check_in'] < $checkOut && $reservation['check_out'] > $checkIn;

            if ($sameRoom && !$sameReservation && $active && $overlaps) {
                return true;
            }
        }

        return false;
    }

    public function dashboardStats(): array
    {
        $reservations = JsonStore::all('reservations');

        return [
            'rooms' => count(JsonStore::all('rooms')),
            'active' => count(array_filter($reservations, fn (array $reservation): bool => $reservation['status'] === 'Em hospedagem')),
            'pending' => count(array_filter($reservations, fn (array $reservation): bool => $reservation['status'] === 'Pendente de confirmacao')),
            'finished' => count(array_filter($reservations, fn (array $reservation): bool => $reservation['status'] === 'Finalizada')),
        ];
    }

    private function withRelations(array $reservation): array
    {
        $guest = JsonStore::find('guests', (int) $reservation['guest_id']) ?? [];
        $room = JsonStore::find('rooms', (int) $reservation['room_id']) ?? [];

        return array_merge($reservation, [
            'guest_name' => $guest['name'] ?? '',
            'email' => $guest['email'] ?? '',
            'phone' => $guest['phone'] ?? '',
            'room_number' => $room['number'] ?? '',
            'room_type' => $room['type'] ?? '',
            'daily_rate' => $room['daily_rate'] ?? 0,
            'capacity' => $room['capacity'] ?? 0,
        ]);
    }
}
