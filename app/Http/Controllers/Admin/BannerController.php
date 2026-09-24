<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        return view('admin.banner.index', ['title' => 'Daftar Banner', 'banner' => Banner::orderBy('urutan')->get()]);
    }

    public function create()
    {
        return view('admin.banner.form', ['title' => 'Tambah Banner', 'row' => null]);
    }

    public function edit($id)
    {
        return view('admin.banner.form', ['title' => 'Ubah Banner', 'row' => Banner::findOrFail($this->decrypt($id))]);
    }

    public function store(Request $request)
    {
        Banner::create($this->payload($request));

        return redirect(site_admin('banner'))->with('msg_flash', success_message('Data banner berhasil disimpan.'));
    }

    public function update(Request $request, $id)
    {
        Banner::findOrFail($this->decrypt($id))->update($this->payload($request));

        return redirect(site_admin('banner'))->with('msg_flash', success_message('Data banner berhasil disimpan.'));
    }

    public function destroy($id)
    {
        Banner::findOrFail($this->decrypt($id))->delete();

        return redirect(site_admin('banner'))->with('msg_flash', success_message('Berhasil dihapus.'));
    }

    private function payload(Request $request): array
    {
        $request->validate(['urutan' => 'required']);

        $file = '';
        if ($request->hasFile('file_banner')) {
            $f = $request->file('file_banner');
            $file = 'banner_'.kode_unik().'_'.date('ymdHis').'.'.$f->getClientOriginalExtension();
            $f->move(public_path('uploads/img'), $file);
        }

        return [
            'nama_banner' => $request->input('bannerName'),
            'link_url' => $request->input('bannerLink'),
            'file_banner' => $file,
            'urutan' => (int) $request->input('urutan'),
            'status' => $request->input('bannerStatus') === 'yes' ? 'yes' : 'no',
        ];
    }

    private function decrypt(string $id): int
    {
        if (ctype_digit($id)) {
            return (int) $id;
        }
        foreach (Banner::pluck('id_banner') as $bid) {
            if (md5((string) $bid) === $id) {
                return (int) $bid;
            }
        }
        abort(404);
    }
}
