<?php

namespace App\Modules\Identity\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Http\Resources\UserResource;
use App\Modules\Identity\Requests\ApiLoginRequest;
use App\Modules\Identity\Services\IdentityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private readonly IdentityService $identity) {}

    /**
     * Authenticate an API client and issue an access token.
     */
    public function login(ApiLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = $this->identity->validateCredentials($data);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        if (! empty($data['device_uuid'])) {
            $this->identity->registerDevice($user, $data);
        }

        $token = $this->identity->issueToken($user, $data['device_name'] ?? 'api-client');

        return response()->json([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'user' => (new UserResource($user))->resolve(),
        ]);
    }

    /**
     * Return the currently authenticated user together with permissions.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => (new UserResource($request->user()))->resolve(),
        ]);
    }

    /**
     * Revoke the access token used for the current request.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
