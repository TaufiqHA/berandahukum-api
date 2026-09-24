<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Hanya level "admin" yang boleh mengakses Data Master, moderasi, pengguna,
 * dan setting pada API admin.
 */
class EnsureApiAdminLevel
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || $user->user_level !== 'admin') {
            return response()->json(['message' => 'Akses khusus admin.'], 403);
        }

        return $next($request);
    }
}
