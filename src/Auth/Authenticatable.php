<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Auth;

use DevBRLucas\LaravelBaseApp\Enums\Auth\WithToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

class Authenticatable extends Model
{
    use HasApiTokens;
    
    public static function login(array $data): static | false
    {
        unset($data['remember']);
        $password = Arr::pull($data, 'password');
        $user = static::query()->where($data)->first();
        if (!$user) return false;
        return Hash::check($password, $user->password) ? $user : false;
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

    public static function response(?WithToken $withToken = null, bool $remember = false): array | false
    {
        $user = Authenticatable::logged();
        if (!$user) return false;
        $class = get_class($user);
        $matches = [];
        preg_match('/(\w+$)/', $class, $matches);
        $type = preg_replace('/(\w)([A-Z])/', '$1-$2', $matches[0]);
        $type = strtolower($type);
        $data = [
            'user' => $user,
            'type' => $type,
        ];
        print_r($data);
        if ($withToken) {
            $data['access_token'] = $withToken === WithToken::CREATE ? $user->generateToken($remember) : $user->refreshToken();
        }
        return $data;
    }
}