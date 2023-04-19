<?php

namespace Skrypt\DeltaSync\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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

    /**
     * Get the related Model for the Event
     */
    public function model(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'model_name', 'model_id');
    }

    /**
     * Get the Updates for the Event
     */
    public function updates(): HasMany
    {
        return $this->hasMany(ModelUpdate::class)->select('id', 'value', 'key');
    }
}
