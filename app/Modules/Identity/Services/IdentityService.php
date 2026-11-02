<?php

namespace App\Modules\Identity\Services;

use App\Modules\Identity\Models\User;
use App\Modules\Identity\Models\UserDevice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;

class IdentityService
{
    /**
     * Authenticate credentials for the stateful web guard and start a session.
     *
     * @param  array<string, mixed>  $credentials
     */
    public function authenticate(array $credentials, bool $remember = false): ?User
    {
        $valid = Auth::guard('web')->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember);

        if (! $valid) {
            return null;
        }

        /** @var User $user */
        $user = Auth::guard('web')->user();
        $this->recordLogin($user);

        return $user;
    }

    /**
     * Validate credentials statelessly, without touching the session.
     * Used by the API login for mobile and NativePHP desktop clients.
     *
     * @param  array<string, mixed>  $credentials
     */
    public function validateCredentials(array $credentials): ?User
    {
        /** @var User|null $user */
        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check((string) $credentials['password'], $user->getAuthPassword())) {
            return null;
        }

        $this->recordLogin($user);

        return $user;
    }

    public function recordLogin(User $user): void
    {
        $user->forceFill(['last_login_at' => now()])->save();
    }

    /**
     * Register (or refresh) a device used to authenticate against the API.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function registerDevice(User $user, array $attributes): UserDevice
    {
        return $user->devices()->updateOrCreate(
            ['device_uuid' => $attributes['device_uuid']],
            [
                'platform' => $attributes['platform'] ?? null,
                'device_name' => $attributes['device_name'] ?? null,
                'app_version' => $attributes['app_version'] ?? null,
                'last_seen_at' => now(),
            ],
        );
    }

    /**
     * Issue an access token carrying the user's permissions as abilities.
     */
    public function issueToken(User $user, string $deviceName): NewAccessToken
    {
        $abilities = $user->permissionSlugs();

        return $user->createToken($deviceName, $abilities ?: ['*']);
    }
}
