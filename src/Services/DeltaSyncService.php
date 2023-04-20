<?php

namespace Skrypt\DeltaSync\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Skrypt\DeltaSync\Models\ModelEvent;
use Skrypt\DeltaSync\Traits\HasDeltaSync;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DeltaSyncService
{
    /**
     * Get the list of models specified in the deltasync config file as model instances.
     *
     * @return Collection
     */
    protected function getDeltaSyncModels(): Collection
    {
        $models = config('deltasync.models');

        return collect($models)->map(function ($model) {
            return app($model);
        });
    }


    public function getSyncId(): int
    {
        return ModelEvent::max('id') ?? 0;
    }

    public function prepareStreamedResponse(Collection $data, int $syncId): StreamedResponse | JsonResponse
    {
        $response = new StreamedResponse();

        $response->setCallback(function () use ($data, $syncId) {
            $data->chunk(200)->each(function ($chunk) use ($syncId) {
                echo json_encode([
                        "data" => $chunk,
                        "lastSyncId" => $syncId
                    ]) . "\n";
            });
        });

        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(200);

        return $response;
    }

    public function getFullSyncData(): array
    {
        $models = $this->getDeltaSyncModels();
        $data = [];

        foreach ($models as $model => $modelClass) {
            if (in_array(HasDeltaSync::class, class_uses_recursive($modelClass))) {
                $syncStrategy = $modelClass->getDeltaSyncStrategy();
                $data[$model] = $syncStrategy->fullSync();
            }
        }

        return $data;
    }

    public function getDeltaSyncData(int $lastSyncId): array
    {
        $models = $this->getDeltaSyncModels();
        $data = [];

        foreach ($models as $model => $modelClass) {
            if (in_array(HasDeltaSync::class, class_uses_recursive($modelClass))) {
                $syncStrategy = $modelClass->getDeltaSyncStrategy();
                $data[$model] = $syncStrategy->deltaSync($lastSyncId);
            }
        }

        return $data;
    }
}
