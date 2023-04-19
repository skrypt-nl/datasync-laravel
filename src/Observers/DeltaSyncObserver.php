<?php

namespace Skrypt\DeltaSync\Observers;

use Illuminate\Database\Eloquent\Model;
use Skrypt\DeltaSync\Enums\EventTypeEnum;
use Skrypt\DeltaSync\Models\ModelEvent;
use Skrypt\DeltaSync\Models\ModelUpdate;

class DeltaSyncObserver
{
    /**
     * Handle the "created" event.
     */
    public function created(Model $model): void
    {
        ModelEvent::create([
            'type' => EventTypeEnum::Create,
            'model_name' => $model->getDeltaSyncModelName(),
            'model_id' =>  $model->id
        ]);
    }

    /**
     * Handle the "updated" event.
     */
    public function updated(Model $model): void
    {
        $modelEvent = ModelEvent::create([
            'type' => EventTypeEnum::Update,
            'model_name' => $model->getDeltaSyncModelName(),
            'model_id' => $model->id
        ]);

        foreach ($model->getChanges() as $key => $value) {
            ModelUpdate::create([
                'key' => $key,
                'value' => $value,
                'model_event_id' => $modelEvent->id
            ]);
        }
    }

    /**
     * Handle the "deleted" event.
     */
    public function deleted(Model $model): void
    {
        ModelEvent::create([
            'type' => EventTypeEnum::Delete,
            'model_name' => $model->getDeltaSyncModelName(),
            'model_id' =>  $model->id
        ]);
    }
}
