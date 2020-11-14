<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\UnauthorisedException;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\JWTGuard;

class AuthController extends Controller
{
    /**
     * The auth guard instance
     *
     * @var JWTGuard
     */
    protected JWTGuard $auth;

    /**
     * Inject the authentication guard (in this case, JWT)
     */
    public function __construct()
    {
        $this->auth = auth('api');
    }

    /**
     * Logs the user in and returns a JWT token
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws UnauthorisedException
     */
    public function token(LoginRequest $request): JsonResponse
    {
        $token = $this->auth->attempt($request->validated());

        if (!$token) {
            throw new UnauthorisedException('Unauthorised');
        }

        return $this->respondWithToken($token);
    }

    /**
     * Refreshes the user token, returning a new one
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken($this->auth->refresh());
    }

    /**
     * Returns the currently logged in user
     *
     * @return JsonResource
     */
    public function user(): JsonResource
    {
        UserResource::withoutWrapping();

        return UserResource::make($this->auth->user());
    }

    /**
     * Logs the user out
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->auth->logout();

        return $this->respondWithBool(true);
    }

    /**
     * Responds with a JWT token
     *
     * @param $token
     * @return JsonResponse
     */
    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
