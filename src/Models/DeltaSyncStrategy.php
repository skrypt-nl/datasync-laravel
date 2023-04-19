<?php

namespace Skrypt\DeltaSync\Models;

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

    public function deltaSyncQuery(): Model|Builder
    {
        return $this->model;
    }

    public function initSync(): Collection
    {
        return $this->deltaSyncQuery()->get();
    }

    public function deltaSync($lastSyncId): Collection
    {
        return $this->deltaSyncQuery()->get();
    }
}
