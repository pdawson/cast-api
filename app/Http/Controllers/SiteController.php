<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SiteRequest;
use App\Http\Resources\SiteResource;
use App\Models\Site;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteController extends ResourceController
{
    /**
     * Returns a listing of entities
     *
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        // Todo: get the domain id from the request and show the records for the
        // requested domain.

        $records = Site::query()->paginate();

        return SiteResource::collection($records);
    }

    /**
     * Returns a JsonResource for the entity
     *
     * @param Site $model
     * @return JsonResource
     */
    public function show(Site $model): JsonResource
    {
        return SiteResource::make($model);
    }

    /**
     * Creates a new entity with the validated data
     *
     * @param SiteRequest $request
     * @return JsonResponse
     */
    public function store(SiteRequest $request): JsonResponse
    {
        $site = Site::create($request->validated());

        // TODO: Sync site settings

        return $this->respondWithModel($site, SiteResource::class);
    }

    /**
     * Updates the entity with the validated data
     *
     * @param SiteRequest $request
     * @param Site $model
     * @return JsonResponse
     */
    public function update(SiteRequest $request, Site $model): JsonResponse
    {
        $updated = $model->update($request->validated());

        // TODO: Sync site settings

        return $this->respondWithBool($updated);
    }

    /**
     * Deletes the entity
     *
     * @param Site $model
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Site $model): JsonResponse
    {
        return $this->respondWithBool($model->delete());
    }
}
