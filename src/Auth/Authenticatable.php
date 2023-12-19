<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

class Authenticatable extends Model
{
    use HasApiTokens;
    
    public static function login(array $data): static | false
    {
        $user = static::query()->where('email', $data['email'])->first();
        if (!$user) return false;
        return Hash::check($data['password'], $user->password) ? $user : false;
    }

    public function generateToken(bool $remember): string
    {
        $this->cleanTokens();
        $expiration = $remember ? null : now()->addHour();
        return $this
                    ->createToken(name: 'access_token', expiresAt: $expiration)
                    ->plainTextToken;
    }

    public static function logout(): void
    {
        static::logged()?->cleanTokens();
    }

    public function cleanTokens(): void
    {
        $this->tokens()->delete();
    }

    public static function logged(): static | null
    {
        /** @var Request */
        $request = App::make(Request::class);
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());
        if (!$accessToken) return null;
        return $accessToken->tokenable()->first();
    }

    public function refreshToken(): string
    {
        /** @var PersonalAccessToken | null */
        $accessToken = static::logged()?->tokens()->first();
        abort_unless(401, $accessToken);
        $remember = !((bool) $accessToken->expires_at);
        return $this->generateToken($remember);
    }
}