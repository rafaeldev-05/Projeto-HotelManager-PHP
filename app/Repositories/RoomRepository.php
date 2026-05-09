<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\JsonStore;
use App\Models\HotelStatus;

final class RoomRepository
{
    public function all(): array
    {
        $rooms = JsonStore::all('rooms');
        usort($rooms, fn (array $a, array $b): int => strcmp($a['number'], $b['number']));
        return $rooms;
    }

    public function find(int $id): ?array
    {
        return JsonStore::find('rooms', $id);
    }

    public function create(array $data): void
    {
        JsonStore::insert('rooms', [
            'number' => $data['number'],
            'type' => $data['type'],
            'capacity' => (int) $data['capacity'],
            'daily_rate' => (float) $data['daily_rate'],
            'status' => $data['status'] ?? HotelStatus::ROOM_AVAILABLE,
        ]);
    }

    public function updateStatus(int $roomId, string $status): void
    {
        JsonStore::update('rooms', $roomId, ['status' => $status]);
    }

    public function availableTypes(): array
    {
        $types = array_unique(array_column($this->all(), 'type'));
        sort($types);
        return $types;
    }

    public function findAvailableForPeriod(string $type, int $people, string $checkIn, string $checkOut): ?array
    {
        $rooms = array_filter($this->all(), function (array $room) use ($type, $people, $checkIn, $checkOut): bool {
            if ($room['type'] !== $type || (int) $room['capacity'] < $people || $room['status'] !== HotelStatus::ROOM_AVAILABLE) {
                return false;
            }

            foreach (JsonStore::all('reservations') as $reservation) {
                $sameRoom = (int) $reservation['room_id'] === (int) $room['id'];
                $active = in_array($reservation['status'], [HotelStatus::RESERVATION_CONFIRMED, HotelStatus::RESERVATION_STAYING], true);
                $overlaps = $reservation['check_in'] < $checkOut && $reservation['check_out'] > $checkIn;

                if ($sameRoom && $active && $overlaps) {
                    return false;
                }
            }

            return true;
        });

        usort($rooms, fn (array $a, array $b): int => $a['daily_rate'] <=> $b['daily_rate']);
        return array_values($rooms)[0] ?? null;
    }
}

