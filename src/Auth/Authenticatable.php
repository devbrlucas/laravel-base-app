<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Auth;

use DevBRLucas\LaravelBaseApp\Enums\Auth\WithToken;
use Illuminate\Database\Eloquent\Builder;
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
    
    public static function login(array $data, ?Builder $builder = null): static | false
    {
        unset($data['remember']);
        $password = Arr::pull($data, 'password');
        $user = ($builder ?? static::query())->where($data)->first();
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

    public static function logged(?array $withoutScopes = null): static | null
    {
        /** @var Request */
        $request = App::make(Request::class);
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());
        if ($accessToken) return $accessToken->tokenable()->withoutGlobalScopes($withoutScopes)->first();
        $userData = json_decode(
            base64_decode($request->query('auth_logged', '')),
            true,
        );
        if (!$userData) return null;
        /** @var PersonalAccessToken */
        $accessToken = PersonalAccessToken::query()
                                                    ->where('tokenable_type', $userData['user_type'])
                                                    ->where('tokenable_id', $userData['user_id'])
                                                    ->first();
        if ($accessToken) return $accessToken->tokenable()->withoutGlobalScopes($withoutScopes)->first();
        return null;
    }

    public function token(): PersonalAccessToken | null
    {
        /** @var Request */
        $request = App::make(Request::class);
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());
        if ($accessToken) return $accessToken;
        $userData = json_decode(
            base64_decode($request->query('auth_logged', '')),
            true,
        );
        if (!$userData) return null;
        /** @var PersonalAccessToken */
        $accessToken = PersonalAccessToken::query()
                                                    ->where('tokenable_type', $userData['user_type'])
                                                    ->where('tokenable_id', $userData['user_id'])
                                                    ->first();
        if ($accessToken) return $accessToken;
        return null;
    }

    public function generateQueryAuthData(): array
    {
        $data = [
            'user_type' => get_class($this),
            'user_id' => $this->id,
        ];
        $data = json_encode($data);
        return ['auth_logged' => base64_encode($data)];
    }

    public function refreshToken(): string
    {
        /** @var PersonalAccessToken | null */
        $accessToken = static::logged()?->tokens()->first();
        abort_unless(401, $accessToken);
        $remember = !((bool) $accessToken->expires_at);
        return $this->generateToken($remember);
    }

    public static function response(?WithToken $withToken = null, bool $remember = false, ?self $user = null): array | false
    {
        if (!$user) $user = Authenticatable::logged();
        if (!$user) return false;
        $class = get_class($user);
        $matches = [];
        preg_match('/(\w+$)/', $class, $matches);
        $type = preg_replace('/(\w)([A-Z])/', '$1_$2', $matches[0]);
        $type = strtolower($type);
        $data = [
            'user' => $user,
            'type' => $type,
        ];
        if ($withToken) {
            $data['access_token'] = $withToken === WithToken::CREATE ? $user->generateToken($remember) : $user->refreshToken();
        }
        return $data;
    }

    protected static function booted(): void
    {
        static::deleted(function (self $model): void {
            $model->cleanTokens();
        });
    }
}