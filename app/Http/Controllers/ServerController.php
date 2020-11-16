<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ServerRequest;
use App\Http\Resources\ServerResource;
use App\Http\Resources\SettingValueResource;
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
     * Returns a JSON response for an id => name representation of the entities records
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $records = Server::query()->pluck('name', 'id');

        return response()->json(['servers' => $records]);
    }

    /**
     * Returns a JSON response for the single entity
     *
     * @param Server $server
     * @return JsonResponse
     */
    public function show(Server $server): JsonResponse
    {
        ServerResource::withoutWrapping();

        return response()->json([
            'server' => ServerResource::make($server),
            'settings' => SettingValueResource::collection($server->settings),
        ]);
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
     * @param Server $server
     * @return JsonResponse
     */
    public function update(ServerRequest $request, Server $server): JsonResponse
    {
        $updated = $server->update($request->validated());

        // TODO: Sync server global settings

        return $this->respondWithBool($updated);
    }

    /**
     * Deletes the entity
     *
     * @param Server $server
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Server $server): JsonResponse
    {
        return $this->respondWithBool($server->delete());
    }
}
