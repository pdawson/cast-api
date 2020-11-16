<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SiteRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\SettingValueResource;
use App\Http\Resources\SiteResource;
use App\Http\Resources\UserResource;
use App\Models\Site;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends ResourceController
{
    /**
     * Returns a listing of entities
     *
     * @param Request $request
     * @return JsonResource
     */
    public function index(Request $request): JsonResource
    {
        $records = User::query()->paginate();

        return UserResource::collection($records);
    }

    /**
     * Returns a JsonResource for the entity
     *
     * @param User $user
     * @return JsonResource
     */
    public function show(User $user): JsonResource
    {
        return UserResource::make($user);
    }

    /**
     * Returns a JSON response for an id => name representation of the entities records
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $records = User::query()->get()->keyBy('id')
            ->map(static function (User $user) {
                return implode(' - ', [$user->name, $user->email]);
            });

        return response()->json(['users' => $records]);
    }

    /**
     * Creates a new entity with the validated data
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return $this->respondWithModel($user, UserResource::class);
    }

    /**
     * Updates the entity with the validated data
     *
     * @param UserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UserRequest $request, User $user): JsonResponse
    {
        return $this->respondWithBool($user->update($request->validated()));
    }

    /**
     * Deletes the entity
     *
     * @param User $user
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            throw new BadRequestHttpException('You cannot delete yourself!');
        }

        return $this->respondWithBool($user->delete());
    }
}
