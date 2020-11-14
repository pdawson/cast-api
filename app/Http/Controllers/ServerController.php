<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ServerRequest;
use App\Http\Resources\ServerResource;
use App\Models\Server;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class ServerController extends ResourceController
{
    /**
     * Returns a listing of entities
     *
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        $records = Server::query()->paginate();

        return ServerResource::collection($records);
    }

    /**
     * Returns a JsonResource for the entity
     *
     * @param Server $model
     * @return JsonResource
     */
    public function show(Server $model): JsonResource
    {
        return ServerResource::make($model);
    }

    /**
     * Creates a new entity with the validated data
     *
     * @param ServerRequest $request
     * @return JsonResponse
     */
    public function store(ServerRequest $request): JsonResponse
    {
        $server = Server::create($request->validated());

        // TODO: Sync server global settings

        return $this->respondWithModel($server, ServerResource::class);
    }

    /**
     * Updates the entity with the validated data
     *
     * @param ServerRequest $request
     * @param Server $model
     * @return JsonResponse
     */
    public function update(ServerRequest $request, Server $model): JsonResponse
    {
        $updated = $model->update($request->validated());

        // TODO: Sync server global settings

        return $this->respondWithBool($updated);
    }

    /**
     * Deletes the entity
     *
     * @param Server $model
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Server $model): JsonResponse
    {
        return $this->respondWithBool($model->delete());
    }
}
