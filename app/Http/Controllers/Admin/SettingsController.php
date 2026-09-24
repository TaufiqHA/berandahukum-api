<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', [
            'title' => 'Seting Informasi',
            'settings' => Setting::where('tipe', 'info')->orderBy('urutan')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.settings.form', ['title' => 'Tambah Setting', 'row' => null]);
    }

    public function edit($id)
    {
        return view('admin.settings.form', ['title' => 'Ubah Setting', 'row' => Setting::findOrFail($id)]);
    }

    public function store(Request $request)
    {
        Setting::create($this->payload($request));

        return redirect(site_admin('settings'))->with('msg_flash', success_message('Data setting berhasil disimpan.'));
    }

    public function update(Request $request, $id)
    {
        Setting::findOrFail($id)->update($this->payload($request));

        return redirect(site_admin('settings'))->with('msg_flash', success_message('Data setting berhasil disimpan.'));
    }

    public function destroy($id)
    {
        Setting::findOrFail($id)->delete();

        return redirect(site_admin('settings'))->with('msg_flash', success_message('Data berhasil dihapus.'));
    }

    private function payload(Request $request): array
    {
        $request->validate(['settingContent' => 'required']);

        return [
            'setting_name' => $request->input('settingName'),
            'setting_content' => $request->input('settingContent'),
            'urutan' => (int) $request->input('settingSort', 0),
            'tipe' => 'info',
            'setting_last_updated' => date('Y-m-d H:i'),
        ];
    }
}
