<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Returns a JSON response with a message
     *
     * @param string $message
     * @return JsonResponse
     */
    public function respondWithMessage(string $message): JsonResponse
    {
        return response()->json(compact('message'));
    }

    /**
     * Returns a JSON response with a boolean value, for the optional key
     *
     * @param bool $value
     * @param string|null $key
     * @return JsonResponse
     */
    public function respondWithBool(bool $value, ?string $key = null): JsonResponse
    {
        $property = $key ?? 'success';

        return response()->json([$property => $value]);
    }

    /**
     * Returns a JSON response with a model resource, useful for when a model is created / updated
     * Contains a success bool which determines whether the action was successful or not
     *
     * @param Model|null $model
     * @param string|null $resource
     * @return JsonResponse
     */
    public function respondWithModel(?Model $model, ?string $resource = null): JsonResponse
    {
        $success = $model !== null;
        $entity = $resource ? call_user_func("$resource::make", $model) : $model;

        return response()->json(
            compact('success', 'entity'),
            $success ? 200 : 500
        );
    }
}
