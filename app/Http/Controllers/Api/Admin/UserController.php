<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends AdminApiController
{
    public function index()
    {
        $rows = User::orderBy('user_id')->get()->map(fn ($u) => [
            'id' => (int) $u->user_id,
            'name' => $u->user_name,
            'email' => $u->user_email,
            'level' => $u->user_level ?: 'user',
        ])->all();

        return $this->ok(['data' => $rows]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|max:100',
            'password' => 'required|string|max:100',
            'level' => 'required|in:admin,user',
        ]);

        User::create([
            'user_name' => $request->input('name'),
            'user_email' => $request->input('email'),
            'user_password' => $request->input('password'),
            'user_level' => $request->input('level'),
        ]);

        return $this->message('Pengguna berhasil disimpan.', 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = [
            'user_name' => $request->input('name'),
            'user_email' => $request->input('email'),
            'user_level' => $request->input('level'),
        ];
        if ($request->filled('password')) {
            $data['user_password'] = $request->input('password');
        }
        $user->update($data);

        return $this->message('Pengguna berhasil diubah.');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return $this->message('Pengguna dihapus.');
    }
}
