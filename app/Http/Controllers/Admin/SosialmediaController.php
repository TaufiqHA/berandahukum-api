<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SysSetting;
use Illuminate\Http\Request;

class SosialmediaController extends Controller
{
    public function index()
    {
        return view('admin.sosialmedia.index', [
            'title' => 'Sosial Media',
            'settings' => Setting::where('tipe', 'sosial')->orderBy('urutan')->get(),
            'setting' => SysSetting::first(),
        ]);
    }

    public function create()
    {
        return view('admin.sosialmedia.form', ['title' => 'Tambah Sosial Media', 'row' => null]);
    }

    public function edit($id)
    {
        return view('admin.sosialmedia.form', ['title' => 'Ubah Sosial Media', 'row' => Setting::findOrFail($id)]);
    }

    public function store(Request $request)
    {
        Setting::create($this->payload($request));

        return redirect(site_admin('sosialmedia'))->with('msg_flash', success_message('Data sosial media berhasil disimpan.'));
    }

    public function update(Request $request, $id)
    {
        Setting::findOrFail($id)->update($this->payload($request));

        return redirect(site_admin('sosialmedia'))->with('msg_flash', success_message('Data sosial media berhasil disimpan.'));
    }

    public function destroy($id)
    {
        Setting::findOrFail($id)->delete();

        return redirect(site_admin('sosialmedia'))->with('msg_flash', success_message('Data berhasil dihapus.'));
    }

    public function updateStatus(Request $request)
    {
        SysSetting::query()->update(['show_youtube' => $request->input('status')]);

        return response()->json(['message' => success_message('Status Tampil berhasil diUpdate.')]);
    }

    private function payload(Request $request): array
    {
        $request->validate(['settingContent' => 'required']);

        return [
            'setting_name' => str_replace(' ', '-', strtolower((string) $request->input('settingName'))),
            'setting_content' => $request->input('settingContent'),
            'urutan' => (int) $request->input('settingSort', 0),
            'tipe' => 'sosial',
            'setting_last_updated' => date('Y-m-d H:i'),
        ];
    }
}
