<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewPath = dirname(__DIR__, 2) . '/resources/views/' . $view . '.php';

        if (!is_file($viewPath)) {
            throw new \RuntimeException("View {$view} nao encontrada.");
        }

        require dirname(__DIR__, 2) . '/resources/views/layouts/app.php';
    }
}

