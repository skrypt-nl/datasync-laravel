<?php

namespace Skrypt\DeltaSync\Strategies;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Skrypt\DeltaSync\Interfaces\DeltaSyncInterface;

class DeltaSyncStrategy implements DeltaSyncInterface
{
    protected Model $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function syncQuery(): Model|Builder
    {
        return $this->model;
    }

    public function fullSync(): Collection
    {
        return $this->syncQuery()->get();
    }

    public function deltaSync($lastSyncId): Collection
    {
        return $this->syncQuery()->get();
    }
}
