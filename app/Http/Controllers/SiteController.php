<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SiteRequest;
use App\Http\Resources\SettingValueResource;
use App\Http\Resources\SiteResource;
use App\Models\Site;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class SiteController extends ResourceController
{
    /**
     * Returns a listing of entities
     *
     * @param Request $request
     * @return JsonResource
     */
    public function index(Request $request): JsonResource
    {
        $query = Site::query();

        if ($request->has('server_id')) {
            $query->where('server_id', $request->get('server_id'));
        }

        return SiteResource::collection($query->paginate());
    }

    /**
     * Returns a JsonResource for the entity
     *
     * @param Site $site
     * @return JsonResponse
     */
    public function show(Site $site): JsonResponse
    {
        SiteResource::withoutWrapping();

        return response()->json([
            'site' => SiteResource::make($site),
            'settings' => SettingValueResource::collection($site->settings),
        ]);
    }

    /**
     * Returns a JSON response for an id => name representation of the entities records
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $records = Site::query()->pluck('name', 'id');

        return response()->json(['sites' => $records]);
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
     * @param Site $site
     * @return JsonResponse
     */
    public function update(SiteRequest $request, Site $site): JsonResponse
    {
        $updated = $site->update($request->validated());

        // TODO: Sync site settings

        return $this->respondWithBool($updated);
    }

    /**
     * Deletes the entity
     *
     * @param Site $site
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Site $site): JsonResponse
    {
        return $this->respondWithBool($site->delete());
    }
}
