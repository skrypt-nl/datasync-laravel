<?php

namespace Skrypt\DeltaSync\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Skrypt\DeltaSync\Models\ModelEvent;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DeltaSyncController extends Controller
{
    private int $syncId;

    public function __construct()
    {
        $this->syncId = $this->getSyncId();
    }

    private function getSyncId(): int
    {
        return ModelEvent::max('id');
    }

    private function response(Collection $data): StreamedResponse
    {
        $response = new StreamedResponse();
        $syncId = $this->syncId;

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

    /**
     * Retrieve a complete data set with all the data the logged-in user has access to.
     *
     * @return StreamedResponse|JsonResponse
     */
    public function full(): StreamedResponse | JsonResponse
    {
        $models = $this->getDeltaSyncModels();

        $data = [];

        foreach ($models as $model) {
            $syncStrategy = $model->getDeltaSyncStrategy();
            $objects = $syncStrategy->fullSync();
            $data[$model->getDeltaSyncModelName()] = $objects;
        }

        return $this->response(collect($data));
    }

    /**
     * Retrieve all data that has been modified after the lastSyncId parameter.
     *
     * @param Request $request
     * @return StreamedResponse|JsonResponse
     */
    public function delta(Request $request): StreamedResponse | JsonResponse
    {
        $models = $this->getDeltaSyncModels();
        $lastSyncId = $request->query('lastSyncId');

        if (!is_numeric($lastSyncId)) {
            return response()->json(['message' => 'Missing or invalid lastSyncId.'], 422);
        }

        $data = [];

        foreach ($models as $model) {
            $syncStrategy = $model->getDeltaSyncStrategy();
            $objects = $syncStrategy->deltaSync($lastSyncId);
            $data[$model->getDeltaSyncModelName()] = $objects;
        }

        return $this->response(collect($data));
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
