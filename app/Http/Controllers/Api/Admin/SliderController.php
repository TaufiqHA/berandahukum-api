<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Article;
use App\Models\Pilihan;
use Illuminate\Http\Request;

/**
 * Pengaturan slider/sorotan beranda dari panel admin mobile.
 * Data disimpan di tbl_pilihan dengan posisi = 'top'.
 */
class SliderController extends AdminApiController
{
    private const POSISI = 'top';

    public function index()
    {
        $rows = Pilihan::where('posisi', self::POSISI)->orderBy('urutan')->orderBy('id')->get()
            ->map(fn ($p) => [
                'id' => (int) $p->id,
                'article_id' => (int) $p->article_id,
                'title' => Article::find($p->article_id)->article_title ?? '(artikel tidak ditemukan)',
                'urutan' => (int) $p->urutan,
            ])->all();

        return $this->ok(['data' => $rows]);
    }

    /** Cari artikel terbit (untuk pemilih artikel). */
    public function articles(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $rows = Article::where('article_status', 1)
            ->when($q !== '', fn ($x) => $x->where('article_title', 'like', '%'.$q.'%'))
            ->orderByDesc('article_id')
            ->limit(30)
            ->get(['article_id', 'article_title'])
            ->map(fn ($a) => ['id' => (int) $a->article_id, 'title' => $a->article_title])
            ->all();

        return $this->ok(['data' => $rows]);
    }

    public function store(Request $request)
    {
        $data = $this->payload($request);
        $data['posisi'] = self::POSISI;
        Pilihan::create($data);

        return $this->message('Artikel slider berhasil ditambahkan.', 201);
    }

    public function update(Request $request, $id)
    {
        Pilihan::where('posisi', self::POSISI)->findOrFail($id)->update($this->payload($request));

        return $this->message('Artikel slider berhasil diubah.');
    }

    public function destroy($id)
    {
        Pilihan::where('posisi', self::POSISI)->findOrFail($id)->delete();

        return $this->message('Artikel slider berhasil dihapus.');
    }

    /** Simpan urutan hasil geser. Body: { position: [id, id, ...] } */
    public function urutan(Request $request)
    {
        foreach ((array) $request->input('position', []) as $i => $id) {
            Pilihan::where('posisi', self::POSISI)->where('id', (int) $id)->update(['urutan' => $i + 1]);
        }

        return $this->message('Urutan slider disimpan.');
    }

    private function payload(Request $request): array
    {
        $request->validate(['article_id' => 'required|integer', 'urutan' => 'nullable|integer']);

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
