<?php

namespace Skrypt\DeltaSync\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

interface DeltaSyncInterface {
    public function deltaSyncQuery(): Model|Builder;
    public function initSync(): Collection;
    public function deltaSync(int $lastSyncId): Collection;
}
