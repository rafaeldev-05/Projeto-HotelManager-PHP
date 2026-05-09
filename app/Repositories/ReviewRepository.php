<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\JsonStore;

final class ReviewRepository
{
    public function all(): array
    {
        $reviews = array_map(function (array $review): array {
            $reservation = JsonStore::find('reservations', (int) $review['reservation_id']) ?? [];
            $guest = JsonStore::find('guests', (int) ($reservation['guest_id'] ?? 0)) ?? [];
            $review['code'] = $reservation['code'] ?? '';
            $review['guest_name'] = $guest['name'] ?? '';
            return $review;
        }, JsonStore::all('reviews'));

        usort($reviews, fn (array $a, array $b): int => strcmp($b['created_at'], $a['created_at']));
        return $reviews;
    }

    public function create(array $data): void
    {
        JsonStore::insert('reviews', $data);
    }
}

