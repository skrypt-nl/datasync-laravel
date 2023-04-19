<?php

namespace Skrypt\DeltaSync\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Skrypt\DeltaSync\Models\DeltaSyncStrategy;

class DeltaSyncController extends Controller
{
    /**
     * Retrieve a complete data set with all the data the logged in user has access to.
     *
     * @return JsonResponse
     */
    public function init(): JsonResponse
    {
        $user = Auth::user();
        $models = $this->getDeltaSyncModels();

        $data = [];

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 403);
        }

        foreach ($models as $model) {
            $syncStrategy = $model->getDeltaSyncStrategy();
            $objects = $syncStrategy->initSync();
            $data[$model->getDeltaSyncModelName()] = $objects;
        }

        return response()->json($data);
    }

//    /**
//     * Retrieve all data that has been modified after the lastSyncId parameter.
//     *
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function delta(Request $request): JsonResponse
//    {
//        $user = Auth::user();
//        $models = $this->getDeltaSyncModels();
//        $lastSyncId = $request->query('lastSyncId', null);
//
//        if (!$lastSyncId) {
//            return response()->json(['message' => 'Missing or invalid lastSyncId.', 422]);
//        }
//
//        $data = [];
//        foreach ($models as $model) {
//            $syncStrategy = $model->getDeltaSyncStrategy();
//            $fields = $model->deltaSyncFields();
//            $filter = $syncStrategy->getDeltaFilter($user, $lastSyncId);
//            $objects = $model->where('updated_at', '>', $lastSyncId)->where($filter)->get();
//            $data[$model->getDeltaSyncModelName()] = $objects;
//        }
//
//        return response()->json($data);
//    }

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
