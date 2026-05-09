<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ProductRepository;

final class ProductController extends Controller
{
    public function index(): void
    {
        $this->view('products/index', [
            'title' => 'Produtos e servicos',
            'products' => (new ProductRepository())->all(),
        ]);
    }

    public function store(): void
    {
        try {
            (new ProductRepository())->create($_POST);
            $this->backWith('success', 'Produto ou servico cadastrado.', '/products');
        } catch (\Throwable $exception) {
            $this->backWith('error', $exception->getMessage(), '/products');
        }
    }
}

