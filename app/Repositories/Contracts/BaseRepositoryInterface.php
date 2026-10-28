<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Model;

    public function create(array $attributes): Model;

    public function update(int $id, array $attributes): bool;

    public function delete(int $id): bool;
}
