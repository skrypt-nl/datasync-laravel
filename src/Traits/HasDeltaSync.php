<?php

namespace Skrypt\DeltaSync\Traits;

use Skrypt\DeltaSync\Observers\DeltaSyncObserver;
use Skrypt\DeltaSync\Strategies\DeltaSyncStrategy;

trait HasDeltaSync {
    protected ?array $deltaSyncFields;

    public static function boot(): void
    {
        parent::boot();
        static::observe(DeltaSyncObserver::class);
    }

    /**
     * Get the DeltaSync access strategy for the model.
     *
     * @return string
     */
    public function getDeltaSyncModelName(): string
    {
        return $this->deltaSyncModelName ?? get_class($this);
    }

    /**
     * Get the DeltaSync access strategy for the model.
     *
     * @return DeltaSyncStrategy
     */
    public function getDeltaSyncStrategy(): DeltaSyncStrategy
    {
        if (isset($this->deltaSyncStrategy)) {
            return app($this->deltaSyncStrategy);
        } else {
            $className = get_class($this);
            $strategyName = '\\App\\Strategies\\' . class_basename($className) . 'DeltaSyncStrategy';
            return new $strategyName($this);
        }
    }

    /**
     * Get the fields to be returned by the DeltaSync endpoints for the model.
     *
     * @return array
     */
    public function deltaSyncFields(): array
    {
        return $this->deltaSyncFields ?? ['*'];
    }
}
