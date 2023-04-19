<?php

namespace Skrypt\DeltaSync\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Skrypt\DeltaSync\Enums\EventTypeEnum;

class ModelEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'type',
        'model_name',
        'model_id'
    ];

    protected $casts = [
        'type' => EventTypeEnum::class
    ];

    protected $with = [
        'modelUpdates'
    ];

    /**
     * Get the Updates for the Event
     */
    public function modelUpdates(): ?HasMany
    {
        return $this->hasMany(ModelUpdate::class);
    }
}
