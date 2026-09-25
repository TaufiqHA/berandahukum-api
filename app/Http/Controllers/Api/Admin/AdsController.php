<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Ads;
use Illuminate\Http\Request;

/**
 * Iklan khusus aplikasi mobile (panel admin mobile), disimpan di tbl_ads:
 *   - 100: antar kategori beranda (bisa dipasangkan ke kategori tertentu)
 *   - 101: bawah beranda, antara banner tengah dan footer (maks. 3)
 * Posisi ini tidak dipakai situs web.
 */
class AdsController extends AdminApiController
{
    /** Posisi iklan yang dikelola dari panel mobile. */
    private const POSISI_ANTAR_KATEGORI = 100;
    private const POSISI_BAWAH_BERANDA = 101;
    private const POSISI_ATAS_BERANDA = 102;
    private const POSISI_ATAS_ARTIKEL = 103;

    private const POSISI = [
        self::POSISI_ANTAR_KATEGORI,
        self::POSISI_BAWAH_BERANDA,
        self::POSISI_ATAS_BERANDA,
        self::POSISI_ATAS_ARTIKEL,
    ];

    /** Batas jumlah iklan per posisi (null = tanpa batas). */
    private const MAX_PER_POSISI = [
        self::POSISI_ANTAR_KATEGORI => null,
        self::POSISI_BAWAH_BERANDA => 3,
        self::POSISI_ATAS_BERANDA => 2,
        self::POSISI_ATAS_ARTIKEL => 2,
    ];

    public function index()
    {
        $rows = Ads::whereIn('ads_position', self::POSISI)
            ->where('ads_status', '1')
            ->orderBy('ads_position')
            ->orderBy('ads_id')
            ->get()
            ->map(fn ($a) => [
                'id' => (int) $a->ads_id,
                'image' => $this->imageUrl((string) $a->ads_url),
                'link' => $a->ads_link !== '' ? $a->ads_link : null,
                'position' => (int) $a->ads_position,
                'category_id' => $a->ads_category_id ? (int) $a->ads_category_id : null,
            ])->all();

        return $this->ok(['data' => $rows]);
    }

    public function store(Request $request)
    {
        $request->validate(['image' => 'required|image', 'position' => 'required|integer']);

        $position = (int) $request->input('position');
        if (! in_array($position, self::POSISI, true)) {
            return $this->message('Penempatan iklan tidak dikenal.', 422);
        }
        if ($this->isFull($position)) {
            return $this->message('Maksimal '.$this->maxFor($position).' iklan untuk penempatan ini.', 422);
        }

        Ads::create([
            'ads_position' => $position,
            'ads_type' => 0,
            'ads_file_type' => 0,
            'ads_url' => $this->storeUpload($request, 'image', 'ads', 'i'),
            'ads_link' => (string) $request->input('link', ''),
            'ads_category_id' => $position === self::POSISI_ANTAR_KATEGORI ? $this->categoryId($request) : null,
            'ads_status' => '1',
        ]);

        return $this->message('Iklan ditambahkan.', 201);
    }

    public function update(Request $request, $id)
    {
        $ads = Ads::whereIn('ads_position', self::POSISI)->findOrFail($id);
        $position = (int) $ads->ads_position;

        $data = ['ads_link' => (string) $request->input('link', '')];
        if ($request->has('category_id') && $position === self::POSISI_ANTAR_KATEGORI) {
            $data['ads_category_id'] = $this->categoryId($request);
        }

        if ($request->hasFile('image')) {
            $request->validate(['image' => 'image']);
            $old = (string) $ads->ads_url;
            $data['ads_url'] = $this->storeUpload($request, 'image', 'ads', 'i');
            $this->removeFile($old);
        }

        $ads->update($data);

        return $this->message('Iklan diubah.');
    }

    public function destroy($id)
    {
        $ads = Ads::whereIn('ads_position', self::POSISI)->findOrFail($id);
        $this->removeFile((string) $ads->ads_url);
        $ads->delete();

        return $this->message('Iklan dihapus.');
    }

    private function maxFor(int $position): ?int
    {
        return self::MAX_PER_POSISI[$position] ?? null;
    }

    private function isFull(int $position): bool
    {
        $max = $this->maxFor($position);
        if ($max === null) {
            return false;
        }

        return Ads::where('ads_position', $position)->where('ads_status', '1')->count() >= $max;
    }

    /** Kategori tujuan (null = bergilir di celah yang belum punya iklan). */
    private function categoryId(Request $request): ?int
    {
        $value = $request->input('category_id');
        if ($value === null || $value === '' || (int) $value <= 0) {
            return null;
        }

        return (int) $value;
    }

    private function imageUrl(string $file): ?string
    {
        if ($file === '') {
            return null;
        }

        return str_starts_with($file, 'http') ? $file : 'uploads/i/'.$file;
    }

    private function removeFile(string $file): void
    {
        if ($file === '' || str_starts_with($file, 'http')) {
            return;
        }
        $path = public_path('uploads/i/'.$file);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
