<?php

declare(strict_types=1);

namespace App\Core;

final class JsonStore
{
    private static string $path;
    private static ?array $data = null;

    public static function configure(string $path): void
    {
        self::$path = $path;
    }

    public static function reset(array $data): void
    {
        self::$data = $data;
        self::save();
    }

    public static function all(string $table): array
    {
        self::load();
        return array_values(self::$data[$table] ?? []);
    }

    public static function find(string $table, int $id): ?array
    {
        foreach (self::all($table) as $row) {
            if ((int) $row['id'] === $id) {
                return $row;
            }
        }

        return null;
    }

    public static function insert(string $table, array $row): int
    {
        self::load();
        $id = self::nextId($table);
        $row['id'] = $id;
        $row['created_at'] ??= date('Y-m-d H:i:s');
        self::$data[$table][] = $row;
        self::save();

        return $id;
    }

    public static function update(string $table, int $id, array $attributes): void
    {
        self::load();

        foreach (self::$data[$table] as $index => $row) {
            if ((int) $row['id'] === $id) {
                self::$data[$table][$index] = array_merge($row, $attributes);
                self::save();
                return;
            }
        }
    }

    public static function where(string $table, callable $callback): array
    {
        return array_values(array_filter(self::all($table), $callback));
    }

    private static function load(): void
    {
        if (self::$data !== null) {
            return;
        }

        if (!is_file(self::$path)) {
            self::$data = self::emptyData();
            self::save();
            return;
        }

        self::$data = json_decode((string) file_get_contents(self::$path), true, flags: JSON_THROW_ON_ERROR);
    }

    private static function save(): void
    {
        $directory = dirname(self::$path);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents(self::$path, json_encode(self::$data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private static function nextId(string $table): int
    {
        $ids = array_column(self::$data[$table] ?? [], 'id');
        return $ids ? max($ids) + 1 : 1;
    }

    private static function emptyData(): array
    {
        return [
            'guests' => [],
            'rooms' => [],
            'products' => [],
            'reservations' => [],
            'consumptions' => [],
            'payments' => [],
            'reviews' => [],
        ];
    }
}

