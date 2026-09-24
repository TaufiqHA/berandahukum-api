<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends AdminApiController
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('user_email', $request->input('email'))->first();
        $password = (string) $request->input('password');

        $valid = $user && (
            $user->user_password === $password
            || (str_starts_with((string) $user->user_password, '$2') && Hash::check($password, $user->user_password))
        );

        if (! $valid) {
            return $this->message('Email atau kata sandi tidak cocok.', 422);
        }

        $token = ApiToken::create([
            'user_id' => $user->user_id,
            'token' => Str::random(60),
            'created_at' => now(),
            'last_used_at' => now(),
        ]);

        return $this->ok([
            'token' => $token->token,
            'user' => $this->userData($user),
        ]);
    }

    public function me()
    {
        return $this->ok(['user' => $this->userData($this->user())]);
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        if ($token) {
            ApiToken::where('token', $token)->delete();
        }

        return $this->message('Berhasil keluar.');
    }

    private function userData(User $u): array
    {
        return [
            'id' => (int) $u->user_id,
            'name' => $u->user_name,
            'email' => $u->user_email,
            'level' => $u->user_level ?: 'user',
            'is_admin' => $u->user_level === 'admin',
        ];
    }
}
