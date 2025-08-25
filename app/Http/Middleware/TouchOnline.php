<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TouchOnline
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $u = auth()->user();
            if (!$u->last_seen_at || $u->last_seen_at->lt(now()->subMinute())) {
                $u->forceFill([
                    'last_seen_at' => now(),
                    'login_status' => 'online', // perpanjang
                ])->save();
            }
        }
        return $next($request);
    }
}
