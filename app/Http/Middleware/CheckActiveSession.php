<?php

namespace App\Http\Middleware;

use App\Models\WorkSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->type != 'user') {
            return $next($request);
        }
        // 2. Si c'est un agent, on vérifie sa session
        $hasActiveSession = WorkSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->exists();
        if (! $hasActiveSession && ! $request->is('sessions*')) {
            return $request->expectsJson()
    ? response()->json(['message' => 'Session requise.'], 403)
    : redirect()->route('sessions.create');
        }

        return $next($request);

    }
}
