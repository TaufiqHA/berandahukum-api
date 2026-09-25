<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HomeLayout;
use Illuminate\Http\Request;

class LayoutController extends Controller
{
    public function index()
    {
        $sections = HomeLayout::sections();
        $layout = HomeLayout::get();

        $rows = array_map(fn ($it) => [
            'key' => $it['key'],
            'label' => $sections[$it['key']]['label'] ?? $it['key'],
            'scope' => $sections[$it['key']]['scope'] ?? 'both',
            'enabled' => (bool) $it['enabled'],
        ], $layout);

        return view('admin.layout.index', [
            'title' => 'Tata Letak Beranda',
            'rows' => $rows,
            'show_ui' => true,
        ]);
    }

    public function save(Request $request)
    {
        $enabled = array_map('strval', (array) $request->input('enabled', []));
        $items = [];

        foreach ((array) $request->input('position', []) as $key) {
            $items[] = ['key' => (string) $key, 'enabled' => in_array((string) $key, $enabled, true)];
        }

        HomeLayout::save($items);

        return redirect(site_admin('layout'))->with('msg_flash', success_message('Tata letak beranda berhasil disimpan.'));
    }
}
