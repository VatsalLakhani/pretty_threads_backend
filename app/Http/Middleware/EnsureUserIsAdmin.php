<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! (bool) ($user->is_admin ?? false)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden: admin access only',
            ], 403);
        }

        return $next($request);
    }
}
