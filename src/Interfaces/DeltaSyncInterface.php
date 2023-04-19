<?php

namespace Skrypt\DeltaSync\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

interface DeltaSyncInterface {
    public function syncQuery(): Model|Builder;
    public function fullSync(): Collection;
    public function deltaSync(int $lastSyncId): Collection;
}
