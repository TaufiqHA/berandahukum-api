<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Autentikasi API berbasis token (Bearer). Dipakai panel admin mobile.
 */
class AuthenticateApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?: $request->input('api_token');

        if (! $token) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $row = ApiToken::where('token', $token)->first();
        $user = $row ? User::find($row->user_id) : null;

        if (! $user) {
            return response()->json(['message' => 'Sesi tidak valid. Silakan masuk kembali.'], 401);
        }

        $row->update(['last_used_at' => now()]);

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
