<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdsPop;
use Illuminate\Http\Request;

class PopupController extends Controller
{
    public function index()
    {
        return view('admin.popup.index', ['title' => 'Seting Iklan PopUp', 'pop' => AdsPop::first()]);
    }

    public function update(Request $request)
    {
        $pop = AdsPop::first();

        $data = [
            'ads_type' => $request->input('adsType'),
            'ads_file_type' => $request->input('adsFileType'),
            'ads_link' => $request->input('ads_link') ?: '#',
            'ads_status' => $request->input('ads_status'),
            'ads_content' => $request->input('ads_content') ?: ' ',
        ];

        if ($request->hasFile('adsFile')) {
            $file = $request->file('adsFile');
            $name = kode_unik().'_'.date('ymdHis').'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/i'), $name);
            $data['ads_url'] = $name;
        } elseif ($request->filled('adsUrl')) {
            $data['ads_url'] = $request->input('adsUrl');
        }

        if ($pop) {
            $pop->update($data);
        } else {
            AdsPop::create($data);
        }

        return redirect(site_admin('popup'))->with('msg_flash', success_message('Data iklan berhasil disimpan.'));
    }
}
