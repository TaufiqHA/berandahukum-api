<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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
        $banner = Banner::findOrFail($this->decrypt($id));
        $banner->update($this->payload($request, $banner));

        return redirect(site_admin('banner'))->with('msg_flash', success_message('Data banner berhasil disimpan.'));
    }

    public function destroy($id)
    {
        Banner::findOrFail($this->decrypt($id))->delete();

        return redirect(site_admin('banner'))->with('msg_flash', success_message('Berhasil dihapus.'));
    }

    private function payload(Request $request, ?Banner $existing = null): array
    {
        $request->validate(['urutan' => 'required', 'bannerType' => 'required']);

        $type = (int) $request->input('bannerType', 0);

        $file = $existing->file_banner ?? '';
        if ($request->hasFile('file_banner')) {
            $f = $request->file('file_banner');
            $file = 'banner_'.kode_unik().'_'.date('ymdHis').'.'.$f->getClientOriginalExtension();
            $f->move(public_path('uploads/img'), $file);
        }

        // Simpan kode/URL bila diisi (jangan buang walaupun tipe belum diganti).
        $content = $request->filled('bannerContent')
            ? $request->input('bannerContent')
            : ($existing->banner_content ?? null);

        // Data lama (sebelum ada kolom banner_content) menyimpan kode script/iframe
        // di link_url. Pindahkan ke banner_content agar form & beranda benar.
        if (($content === null || trim((string) $content) === '') && $existing && $type !== 0) {
            $legacy = (string) ($existing->link_url ?? '');
            if ($legacy !== '' && preg_match('/<[a-z!\/]/i', $legacy)) {
                $content = $legacy;
            }
        }

        $data = [
            'nama_banner' => $request->input('bannerName'),
            'file_banner' => $file,
            'urutan' => (int) $request->input('urutan'),
            'status' => $request->input('bannerStatus') === 'yes' ? 'yes' : 'no',
        ];

        if ($this->hasTypeColumns()) {
            $data['banner_type'] = $type;
            $data['banner_content'] = $content;
            // link_url hanya dipakai oleh tipe Gambar.
            $data['link_url'] = $type === 0 ? $request->input('bannerLink') : null;
        } else {
            // Skema lama (kolom banner_type/banner_content belum ada):
            // simpan script/iframe di link_url agar tidak error saat menyimpan.
            $data['link_url'] = $type === 0 ? $request->input('bannerLink') : $content;
        }

        return $data;
    }

    /**
     * Kolom banner_type & banner_content ditambahkan lewat migrasi.
     * Cek agar penyimpanan tetap jalan walau migrasi belum dijalankan.
     */
    private function hasTypeColumns(): bool
    {
        return Schema::hasColumn('tbl_banner', 'banner_type')
            && Schema::hasColumn('tbl_banner', 'banner_content');
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
