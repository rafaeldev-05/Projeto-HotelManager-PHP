<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HotelStatus;
use App\Repositories\ReservationRepository;
use App\Repositories\ReviewRepository;
use InvalidArgumentException;

final class ReviewService
{
    public function __construct(
        private ?ReservationRepository $reservations = null,
        private ?ReviewRepository $reviews = null,
    ) {
        $this->reservations ??= new ReservationRepository();
        $this->reviews ??= new ReviewRepository();
    }

    public function create(array $input): void
    {
        $reservation = $this->reservations->find((int) ($input['reservation_id'] ?? 0));
        $rating = (int) ($input['rating'] ?? 0);

        if (!$reservation) {
            throw new InvalidArgumentException('Reserva nao encontrada.');
        }

        if ($reservation['status'] !== HotelStatus::RESERVATION_FINISHED) {
            throw new InvalidArgumentException('A avaliacao so pode ser registrada para reserva finalizada.');
        }

        if ($rating < 1 || $rating > 5) {
            throw new InvalidArgumentException('A nota deve estar entre 1 e 5.');
        }

        $this->reviews->create([
            'reservation_id' => $reservation['id'],
            'rating' => $rating,
            'comment' => trim((string) ($input['comment'] ?? '')),
        ]);
    }
}

