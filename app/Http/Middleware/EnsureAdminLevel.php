<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Hanya user dengan level "admin" yang boleh mengakses Data Master & Setting,
 * padanan Display::cekAkses() pada aplikasi lama.
 */
class EnsureAdminLevel
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || $user->user_level !== 'admin') {
            return redirect(site_admin('dashboard'));
        }

        return $next($request);
    }
}
