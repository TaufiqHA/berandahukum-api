<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.user.index', ['title' => 'Daftar Pengguna', 'user' => User::orderBy('user_id')->get()]);
    }

    public function create()
    {
        return view('admin.user.form', ['title' => 'Tambah Pengguna', 'row' => null]);
    }

    public function edit($id)
    {
        return view('admin.user.form', ['title' => 'Ubah Pengguna', 'row' => User::findOrFail($id)]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'userName' => 'required|max:100',
            'userEmail' => 'required|max:100',
            'userPassword' => 'required|max:100',
            'userLevel' => 'required',
        ]);

        User::create($request->only('user_name', 'user_email', 'user_password', 'user_level') + [
            'user_name' => $request->input('userName'),
            'user_email' => $request->input('userEmail'),
            'user_password' => $request->input('userPassword'),
            'user_level' => $request->input('userLevel'),
        ]);

        return redirect(site_admin('user'))->with('msg_flash', success_message('Data berhasil disimpan.'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = [
            'user_name' => $request->input('userName'),
            'user_email' => $request->input('userEmail'),
            'user_level' => $request->input('userLevel'),
        ];
        if ($request->filled('userPassword')) {
            $data['user_password'] = $request->input('userPassword');
        }
        $user->update($data);

        return redirect(site_admin('user'))->with('msg_flash', success_message('Data berhasil diubah.'));
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return redirect(site_admin('user'))->with('msg_flash', success_message('User berhasil dihapus.'));
    }
}
