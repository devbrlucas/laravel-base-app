<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class ValidateAuthToken
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());
        abort_unless($accessToken, 401);
        if ($accessToken->expires_at) {
            if (now()->greaterThanOrEqualTo($accessToken->expires_at)) {
                $accessToken->delete();
                abort(401);
            }
            $accessToken->update(['expires_at' => now()->addHour()]);
        }
        return $next($request);
    }
}
