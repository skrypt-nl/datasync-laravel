<?php

namespace Skrypt\DeltaSync\Strategies;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Skrypt\DeltaSync\Enums\EventTypeEnum;
use Skrypt\DeltaSync\Interfaces\DeltaSyncInterface;
use Skrypt\DeltaSync\Models\ModelEvent;

class DeltaSyncStrategy implements DeltaSyncInterface
{
    protected Model $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function syncQuery(): Builder
    {
        return $this->model->query();
    }

    public function fullSync(): Collection
    {
        return $this->syncQuery()->get();
    }

    public function deltaSync($lastSyncId): Collection
    {
        $deltaSyncModelName = $this->model->getDeltaSyncModelName();

        $subQuery = str_replace('select *', 'select id', $this->syncQuery()->toSql());
        $bindings = $this->syncQuery()->getBindings();

        return ModelEvent::where('model_name', $deltaSyncModelName)
            ->with(['model', 'updates'])
            ->where('id', '>', $lastSyncId)
            ->whereRaw("`model_id` IN ($subQuery)", $bindings)
            ->get();
    }
}
