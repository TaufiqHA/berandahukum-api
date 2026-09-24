<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SysSetting;
use Illuminate\Http\Request;

class SettingemailController extends Controller
{
    public function index()
    {
        return redirect(site_admin('settingemail/edit'));
    }

    public function edit()
    {
        return view('admin.settingemail.form', [
            'title' => 'Setting Email',
            'setting' => SysSetting::first(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'smtp_host' => 'required',
            'smtp_port' => 'required',
            'smtp_username' => 'required',
            'smtp_password' => 'required',
            'smtp_secure' => 'required',
        ]);

        SysSetting::query()->update($request->only('smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_secure'));

        return redirect(site_admin('settingemail/edit'))->with('msg_flash', success_message('Data setting berhasil disimpan.'));
    }
}
