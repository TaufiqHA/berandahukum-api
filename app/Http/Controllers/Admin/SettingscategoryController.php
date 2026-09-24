<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class SettingscategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post') && is_array($request->input('position'))) {
            foreach ($request->input('position') as $key => $categoryId) {
                Category::where('category_id', $categoryId)->update(['urutan' => $key + 1]);
            }

            return redirect(site_admin('settingscategory'))->with('msg_flash', success_message('Perubahan berhasil disimpan.'));
        }

        return view('admin.settingscategory.index', [
            'title' => 'Seting Urutan Kategori',
            'category' => Category::orderBy('urutan')->get(),
            'show_ui' => true,
        ]);
    }
}
