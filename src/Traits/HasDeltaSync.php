<?php

namespace Skrypt\DeltaSync\Traits;

trait HasDeltaSync {
    protected ?string $deltaSyncStrategy = null;

    public function getDeltaSyncStrategy()
    {
        if (!is_null($this->deltaSyncStrategy)) {
            return $this->deltaSyncStrategy;
        }

        $strategyClass = config('deltasync.access_strategies.' . get_class($this));
        $this->deltaSyncStrategy = app($strategyClass);

        return $this->deltaSyncStrategy;
    }
}
