<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\JsonStore;

final class ProductRepository
{
    public function all(): array
    {
        $products = JsonStore::all('products');
        usort($products, fn (array $a, array $b): int => strcmp($a['name'], $b['name']));
        return $products;
    }

    public function available(): array
    {
        return array_values(array_filter($this->all(), fn (array $product): bool => (int) $product['available'] === 1));
    }

    public function find(int $id): ?array
    {
        return JsonStore::find('products', $id);
    }

    public function create(array $data): void
    {
        JsonStore::insert('products', [
            'name' => trim((string) $data['name']),
            'description' => trim((string) ($data['description'] ?? '')),
            'price' => (float) $data['price'],
            'available' => isset($data['available']) ? 1 : 0,
        ]);
    }
}

