<?php

namespace Skrypt\DeltaSync\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Skrypt\DeltaSync\Services\DeltaSyncService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DeltaSyncController extends Controller
{
    private int $syncId;
    private DeltaSyncService $deltaSyncService;

    public function __construct()
    {
        $this->deltaSyncService = new DeltaSyncService();
        $this->syncId = $this->deltaSyncService->getSyncId();
    }

    /**
     * Retrieve a complete data set with all the data the logged-in user has access to.
     *
     * @return StreamedResponse|JsonResponse
     */
    public function full(): StreamedResponse | JsonResponse
    {
        $data = $this->deltaSyncService->getFullSyncData();

        return $this->deltaSyncService->prepareStreamedResponse(collect($data), $this->syncId);
    }

    /**
     * Retrieve all data that has been modified after the lastSyncId parameter.
     *
     * @param Request $request
     * @return StreamedResponse|JsonResponse
     */
    public function delta(Request $request): StreamedResponse | JsonResponse
    {
        $lastSyncId = $request->query('lastSyncId');

        if (!is_numeric($lastSyncId)) {
            return response()->json(['message' => 'Missing or invalid lastSyncId.'], 422);
        }

        $data = $this->deltaSyncService->getDeltaSyncData($lastSyncId);

        return $this->deltaSyncService->prepareStreamedResponse(collect($data), $this->syncId);
    }
}
