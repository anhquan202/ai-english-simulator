<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function update(int $id, array $attributes): bool
    {
        $record = $this->findById($id);

        if (! $record) {
            return false;
        }

        return $record->update($attributes);
    }

    public function delete(int $id): bool
    {
        $record = $this->findById($id);

        if (! $record) {
            return false;
        }

        return (bool) $record->delete();
    }
}
