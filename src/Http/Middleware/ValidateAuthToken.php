<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Http\Middleware;

use Closure;
use DevBRLucas\LaravelBaseApp\Auth\Authenticatable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateAuthToken
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Authenticatable::logged();
        abort_unless($user, 401);
        $accessToken = $user->token();
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