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
        $response = $next($request);
        $user = Authenticatable::logged();
        if (!$user) return $response;
        $class = get_class($user);
        $matches = [];
        preg_match('/(\w+$)/', $class, $matches);
        $type = preg_replace('/(\w)([A-Z])/', '$1-$2', $matches[0]);
        $type = strtolower($type);
        $data = [
            'user' => $user,
            'type' => $type,
        ];
        $data = json_encode($data);
        $response->headers->set('App-Current-User', $data);
        $exposedHeaders = $response->headers->get('Access-Control-Expose-Headers', '');
        $response->headers->set('Access-Control-Expose-Headers', "App-Current-User,$exposedHeaders");
        return $response;
    }
}
