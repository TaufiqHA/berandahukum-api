<?php

namespace App\Http\Controllers\Api\Admin;

use App\Services\HomeLayout;
use Illuminate\Http\Request;

/**
 * Tata letak halaman beranda dari panel admin mobile.
 * Mengelola App\Services\HomeLayout (tbl_settings tipe "layout").
 */
class LayoutController extends AdminApiController
{
    public function index()
    {
        $sections = HomeLayout::sections();

        $rows = array_map(fn ($it) => [
            'key' => $it['key'],
            'label' => $sections[$it['key']]['label'] ?? $it['key'],
            'scope' => $sections[$it['key']]['scope'] ?? 'both',
            'enabled' => (bool) $it['enabled'],
        ], HomeLayout::get());

        return $this->ok(['data' => $rows]);
    }

    public function save(Request $request)
    {
        $items = [];

        foreach ((array) $request->input('items', []) as $it) {
            if (! is_array($it) || empty($it['key'])) {
                continue;
            }
            $items[] = ['key' => (string) $it['key'], 'enabled' => (bool) ($it['enabled'] ?? true)];
        }

        HomeLayout::save($items);

        return $this->message('Tata letak beranda disimpan.');
    }
}
