<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;

class SettingController extends BaseApiController
{
    public function index()
    {
        return response()->json([
            'info' => Setting::where('tipe', 'info')->orderBy('urutan')->get()->map(fn ($s) => [
                'id' => md5($s->setting_id),
                'name' => $s->setting_name,
            ])->all(),
            'sosial' => Setting::where('tipe', 'sosial')->orderBy('urutan')->get()->map(fn ($s) => [
                'name' => $s->setting_name,
                'url' => $s->setting_content,
            ])->all(),
        ]);
    }

    public function show(string $id)
    {
        $setting = Setting::where('tipe', 'info')->get()->first(fn ($s) => md5($s->setting_id) === $id);
        if (! $setting) {
            return response()->json(['message' => 'Halaman tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => md5($setting->setting_id),
            'name' => $setting->setting_name,
            'content' => $setting->setting_content,
            'updated' => $setting->setting_last_updated,
        ]);
    }
}
