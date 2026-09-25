<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Pilihan;
use Illuminate\Http\Request;

/**
 * Pengaturan artikel yang tampil pada slider/sorotan beranda.
 * Data disimpan di tbl_pilihan dengan posisi = 'top' (dipakai
 * FrontService::articlePilihan('top')).
 */
class SliderController extends Controller
{
    private const POSISI = 'top';

    public function index()
    {
        $rows = Pilihan::where('posisi', self::POSISI)
            ->orderBy('urutan')->orderBy('id')->get()
            ->map(function ($p) {
                $p = $p->toArray();
                $p['article_title'] = Article::find($p['article_id'])->article_title ?? '(artikel tidak ditemukan)';

                return $p;
            });

        return view('admin.slider.index', [
            'title' => 'Slider (Sorotan)',
            'datapilihan' => $rows,
            'show_ui' => true,
        ]);
    }

    public function create()
    {
        return view('admin.slider.form', [
            'title' => 'Tambah Artikel Slider',
            'row' => null,
            'articleTitle' => null,
        ]);
    }

    public function edit($id)
    {
        $row = Pilihan::where('posisi', self::POSISI)->findOrFail($id);

        return view('admin.slider.form', [
            'title' => 'Ubah Artikel Slider',
            'row' => $row,
            'articleTitle' => Article::find($row->article_id)->article_title ?? null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->payload($request);
        $data['posisi'] = self::POSISI;
        Pilihan::create($data);

        return redirect(site_admin('slider'))->with('msg_flash', success_message('Artikel slider berhasil ditambahkan.'));
    }

    public function update(Request $request, $id)
    {
        Pilihan::where('posisi', self::POSISI)->findOrFail($id)->update($this->payload($request));

        return redirect(site_admin('slider'))->with('msg_flash', success_message('Artikel slider berhasil diperbarui.'));
    }

    public function destroy($id)
    {
        Pilihan::where('posisi', self::POSISI)->findOrFail($id)->delete();

        return redirect(site_admin('slider'))->with('msg_flash', success_message('Artikel slider berhasil dihapus.'));
    }

    /** Simpan urutan hasil drag-and-drop. */
    public function urutan(Request $request)
    {
        foreach ((array) $request->input('position', []) as $i => $id) {
            Pilihan::where('posisi', self::POSISI)->where('id', (int) $id)->update(['urutan' => $i + 1]);
        }

        return response()->json(['status' => true]);
    }

    /** AJAX untuk select2: cari artikel yang sudah terbit. */
    public function articles(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $rows = Article::where('article_status', 1)
            ->when($q !== '', fn ($x) => $x->where('article_title', 'like', '%'.$q.'%'))
            ->orderByDesc('article_id')
            ->limit(30)
            ->get(['article_id', 'article_title']);

        return response()->json([
            'results' => $rows->map(fn ($a) => ['id' => $a->article_id, 'text' => $a->article_title])->all(),
        ]);
    }

    private function payload(Request $request): array
    {
        $request->validate([
            'article_id' => 'required|integer',
            'urutan' => 'nullable|integer',
        ]);

        return [
            'article_id' => (int) $request->input('article_id'),
            'urutan' => (int) ($request->input('urutan') ?: $this->nextUrutan()),
        ];
    }

    private function nextUrutan(): int
    {
        return ((int) Pilihan::where('posisi', self::POSISI)->max('urutan')) + 1;
    }
}
