<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Setting;
use App\Models\SysSetting;
use Illuminate\Http\Request;

class SettingsController extends AdminApiController
{
    public function index()
    {
        $rows = Setting::orderBy('urutan')->orderBy('setting_id')->get()->map(fn ($s) => [
            'id' => (int) $s->setting_id,
            'name' => $s->setting_name,
            'content' => $s->setting_content,
            'tipe' => $s->tipe,
            'urutan' => (int) $s->urutan,
        ])->all();

        $sys = SysSetting::first();

        return $this->ok([
            'data' => $rows,
            'sys' => [
                'show_pertanyaan' => ($sys->show_pertanyaan ?? 'yes') === 'yes',
                'show_youtube' => ($sys->show_youtube ?? 'yes') === 'yes',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string', 'content' => 'required|string']);
        Setting::create([
            'setting_name' => $request->input('name'),
            'setting_content' => $request->input('content'),
            'tipe' => $request->input('tipe', 'info'),
            'urutan' => (int) $request->input('urutan', 0),
            'setting_last_updated' => now(),
        ]);

        return $this->message('Informasi berhasil disimpan.', 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string', 'content' => 'required|string']);
        Setting::findOrFail($id)->update([
            'setting_name' => $request->input('name'),
            'setting_content' => $request->input('content'),
            'tipe' => $request->input('tipe', 'info'),
            'urutan' => (int) $request->input('urutan', 0),
            'setting_last_updated' => now(),
        ]);

        return $this->message('Informasi berhasil diubah.');
    }

    public function destroy($id)
    {
        Setting::findOrFail($id)->delete();

        return $this->message('Informasi dihapus.');
    }

    public function updateSys(Request $request)
    {
        $sys = SysSetting::first();
        if (! $sys) {
            return $this->message('Pengaturan sistem tidak ditemukan.', 404);
        }

        $sys->update([
            'show_pertanyaan' => $request->boolean('show_pertanyaan') ? 'yes' : 'no',
            'show_youtube' => $request->boolean('show_youtube') ? 'yes' : 'no',
        ]);

        return $this->message('Pengaturan sistem disimpan.');
    }
}
