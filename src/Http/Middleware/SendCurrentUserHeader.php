<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Http\Middleware;

use Closure;
use DevBRLucas\LaravelBaseApp\Auth\Authenticatable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SendCurrentUserHeader
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $callback = config('laravel-base-app.send_user_callback');
        $response = $next($request);
        $user = Authenticatable::logged();
        $data = $callback ? $callback($user) : Authenticatable::response(user: $user);
        if (!$data) return $response;
        $data = json_encode($data);
        $response->headers->set('App-Current-User', $data);
        $exposedHeaders = $response->headers->get('Access-Control-Expose-Headers', '');
        $response->headers->set('Access-Control-Expose-Headers', "App-Current-User,$exposedHeaders");
        return $response;
    }
}
