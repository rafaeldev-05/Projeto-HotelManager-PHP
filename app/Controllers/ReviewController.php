<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ReservationRepository;
use App\Repositories\ReviewRepository;
use App\Services\ReviewService;

final class ReviewController extends Controller
{
    public function create(): void
    {
        $this->view('reviews/create', [
            'title' => 'Avaliacoes',
            'reservations' => (new ReservationRepository())->all(),
            'reviews' => (new ReviewRepository())->all(),
        ]);
    }

    public function store(): void
    {
        try {
            (new ReviewService())->create($_POST);
            $this->backWith('success', 'Avaliacao registrada.', '/reviews/create');
        } catch (\Throwable $exception) {
            $this->backWith('error', $exception->getMessage(), '/reviews/create');
        }
    }
}

