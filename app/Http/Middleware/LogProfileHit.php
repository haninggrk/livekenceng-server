<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogProfileHit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post')) {
            $email = $request->input('email', $request->header('X-Email'));
            $email = is_string($email) ? trim($email) : null;

            Log::info('ProfileHit', [
                'email' => $email,
                'ip' => $request->ip(),
                'agent' => $request->userAgent(),
                'time' => now()->format('Y-m-d H:i:s'),
            ]);
        }

        return $next($request);
    }
}
