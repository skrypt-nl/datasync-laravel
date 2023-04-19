<?php

namespace Skrypt\DeltaSync\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;

class DeltaSyncController extends Controller
{
    /**
     * Retrieve a complete data set with all the data the logged in user has access to.
     *
     * @return JsonResponse
     */
    public function full(): JsonResponse
    {
        $models = $this->getDeltaSyncModels();

        $data = [];

        foreach ($models as $model) {
            $syncStrategy = $model->getDeltaSyncStrategy();
            $objects = $syncStrategy->fullSync();
            $data[$model->getDeltaSyncModelName()] = $objects;
        }

        return response()->json($data);
    }

    /**
     * Retrieve all data that has been modified after the lastSyncId parameter.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delta(Request $request): JsonResponse
    {
        $models = $this->getDeltaSyncModels();
        $lastSyncId = $request->query('lastSyncId', null);

        if (!$lastSyncId) {
            return response()->json(['message' => 'Missing or invalid lastSyncId.'], 422);
        }

        $data = [];

        foreach ($models as $model) {
            $syncStrategy = $model->getDeltaSyncStrategy();
            $objects = $syncStrategy->deltaSync($lastSyncId);
            $data[$model->getDeltaSyncModelName()] = $objects;
        }

        return response()->json($data);
    }

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
}
