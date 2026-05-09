<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function backWith(string $type, string $message, string $fallback = '/'): void
    {
        $separator = str_contains($fallback, '?') ? '&' : '?';
        $this->redirect($fallback . $separator . http_build_query([$type => $message]));
    }
}

