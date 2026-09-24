<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function info(string $id, Request $request)
    {
        $setting = Setting::where('tipe', 'info')->get()
            ->first(fn ($s) => md5($s->setting_id) === $id);

        if (! $setting) {
            abort(404);
        }

        return view('front.info', [
            'title' => $setting->setting_name.' - Beranda Hukum',
            'setting' => $setting->toArray(),
        ]);
    }
}
