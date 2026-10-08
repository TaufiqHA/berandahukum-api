<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Ads;
use Illuminate\Http\Request;

/**
 * Iklan khusus aplikasi mobile (panel admin mobile), disimpan di tbl_ads:
 *   - 100: antar kategori beranda (bisa dipasangkan ke kategori tertentu)
 *   - 101: bawah beranda, antara banner tengah dan footer (maks. 3)
 *   - 102: atas beranda, di bawah menu (maks. 2)
 *   - 103: di atas artikel (maks. 2)
 *   - 104: interstitial, tampil saat membuka artikel (maks. 1)
 *   - 105: app open, tampil saat aplikasi dibuka (maks. 1)
 *   - 106: reward, ditonton atas aksi pengguna (maks. 1)
 * Posisi ini tidak dipakai situs web.
 *
 * Kolom ads_kind menyimpan format:
 *   0 = gambar, 1 = AdMob native, 2 = banner, 3 = interstitial,
 *   4 = app open, 5 = reward.
 */
class AdsController extends AdminApiController
{
    /** Posisi iklan yang dikelola dari panel mobile. */
    private const POSISI_ANTAR_KATEGORI = 100;

    private const POSISI_BAWAH_BERANDA = 101;

    private const POSISI_ATAS_BERANDA = 102;

    private const POSISI_ATAS_ARTIKEL = 103;

    private const POSISI_INTERSTITIAL = 104;

    private const POSISI_APP_OPEN = 105;

    private const POSISI_REWARD = 106;

    private const POSISI = [
        self::POSISI_ANTAR_KATEGORI,
        self::POSISI_BAWAH_BERANDA,
        self::POSISI_ATAS_BERANDA,
        self::POSISI_ATAS_ARTIKEL,
        self::POSISI_INTERSTITIAL,
        self::POSISI_APP_OPEN,
        self::POSISI_REWARD,
    ];

    /** Batas jumlah iklan per posisi (null = tanpa batas). */
    private const MAX_PER_POSISI = [
        self::POSISI_ANTAR_KATEGORI => null,
        self::POSISI_BAWAH_BERANDA => 3,
        self::POSISI_ATAS_BERANDA => 2,
        self::POSISI_ATAS_ARTIKEL => 2,
        self::POSISI_INTERSTITIAL => 1,
        self::POSISI_APP_OPEN => 1,
        self::POSISI_REWARD => 1,
    ];

    /** Format yang diinput panel → nilai ads_kind. */
    private const KIND_TO_ADS = [
        'image' => 0,
        'admob' => 1,
        'admob_banner' => 2,
        'admob_interstitial' => 3,
        'admob_app_open' => 4,
        'admob_reward' => 5,
    ];

    /** ads_kind → `type` yang dikirim ke aplikasi. */
    private const TYPE_BY_ADS_KIND = [
        0 => 'image',
        1 => 'admob',
        2 => 'admob_banner',
        3 => 'admob_interstitial',
        4 => 'admob_app_open',
        5 => 'admob_reward',
    ];

    /**
     * Format full-screen dipetakan ke posisi tetap (abaikan posisi dari form),
     * karena letaknya hanya relevan lewat pemicu di aplikasi.
     */
    private const FULLSCREEN_POSITION = [
        'admob_interstitial' => self::POSISI_INTERSTITIAL,
        'admob_app_open' => self::POSISI_APP_OPEN,
        'admob_reward' => self::POSISI_REWARD,
    ];

    public function index()
    {
        $rows = Ads::whereIn('ads_position', self::POSISI)
            ->where('ads_status', '1')
            ->orderBy('ads_position')
            ->orderBy('ads_urutan')
            ->orderBy('ads_id')
            ->get()
            ->map(fn ($a) => [
                'id' => (int) $a->ads_id,
                'type' => self::TYPE_BY_ADS_KIND[(int) $a->ads_kind] ?? 'image',
                'image' => (int) $a->ads_kind > 0 ? null : $this->imageUrl((string) $a->ads_url),
                'admob_unit' => $a->ads_admob_unit ?: null,
                'link' => $a->ads_link !== '' && $a->ads_link !== '#' ? $a->ads_link : null,
                'position' => (int) $a->ads_position,
                'category_id' => $a->ads_category_id ? (int) $a->ads_category_id : null,
                'urutan' => (int) $a->ads_urutan,
            ])->all();

        return $this->ok(['data' => $rows]);
    }

    public function store(Request $request)
    {
        $kind = $this->normalizeKind($request->input('kind'));
        $request->merge(['kind' => $kind]);

        $request->validate([
            'kind' => 'required|in:'.implode(',', array_keys(self::KIND_TO_ADS)),
            'position' => 'required|integer',
            'image' => 'required_if:kind,image|image',
            'admob_unit' => 'required_unless:kind,image|nullable|string|max:191',
        ]);

        $adsKind = self::KIND_TO_ADS[$kind];
        $isAdmob = $adsKind > 0;

        $position = $this->positionFor($kind, $request);
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
            'ads_kind' => $adsKind,
            'ads_admob_unit' => $isAdmob ? (string) $request->input('admob_unit') : null,
            'ads_url' => $isAdmob ? '' : (string) $this->storeUpload($request, 'image', 'ads', 'i'),
            'ads_link' => $isAdmob ? '' : (string) $request->input('link', ''),
            'ads_category_id' => $position === self::POSISI_ANTAR_KATEGORI ? $this->categoryId($request) : null,
            'ads_urutan' => $this->nextUrutan($position),
            'ads_status' => '1',
        ]);

        return $this->message('Iklan ditambahkan.', 201);
    }

    /**
     * Simpan urutan iklan hasil geser pada satu penempatan.
     * Body: { position: <int penempatan>, ids: [id, id, ...] }
     * Pengurutan difilter per penempatan agar iklan tidak berpindah tempat.
     */
    public function urutan(Request $request)
    {
        $request->validate([
            'position' => 'required|integer',
            'ids' => 'array',
        ]);

        $position = (int) $request->input('position');
        if (! in_array($position, self::POSISI, true)) {
            return $this->message('Penempatan iklan tidak dikenal.', 422);
        }

        $ids = array_values(array_map('intval', (array) $request->input('ids', [])));

        // Batasi hanya iklan pada penempatan ini.
        $valid = Ads::where('ads_position', $position)->whereIn('ads_id', $ids)->pluck('ads_id')->all();
        $ids = array_values(array_filter($ids, fn ($id) => in_array($id, $valid, true)));

        foreach ($ids as $i => $id) {
            Ads::where('ads_id', $id)->update(['ads_urutan' => $i + 1]);
        }

        // Iklan lain pada penempatan ini diletakkan setelahnya agar urutan lama
        // (0) tidak menyerobot posisi teratas.
        $offset = count($ids);
        Ads::where('ads_position', $position)->whereNotIn('ads_id', $ids)
            ->orderBy('ads_urutan')->orderBy('ads_id')
            ->pluck('ads_id')
            ->each(fn ($id, $k) => Ads::where('ads_id', $id)->update(['ads_urutan' => $offset + $k + 1]));

        return $this->message('Urutan iklan disimpan.');
    }

    public function update(Request $request, $id)
    {
        $ads = Ads::whereIn('ads_position', self::POSISI)->findOrFail($id);
        $position = (int) $ads->ads_position;
        $currentKind = self::TYPE_BY_ADS_KIND[(int) $ads->ads_kind] ?? 'image';
        $currentIsAdmob = (int) $ads->ads_kind > 0;

        $kind = $this->normalizeKind($request->input('kind', $currentKind));
        $request->merge(['kind' => $kind]);
        $adsKind = self::KIND_TO_ADS[$kind];
        $isAdmob = $adsKind > 0;

        $data = [];

        // Penempatan boleh diubah dari form edit. Format full-screen dipetakan
        // ke posisi tetap. Saat pindah penempatan, iklan ditempatkan di urutan
        // terakhir kelompok barunya.
        $newPosition = $this->positionFor($kind, $request, $position);
        if (! in_array($newPosition, self::POSISI, true)) {
            return $this->message('Penempatan iklan tidak dikenal.', 422);
        }
        if ($newPosition !== $position) {
            $max = $this->maxFor($newPosition);
            if ($max !== null
                && Ads::where('ads_position', $newPosition)->where('ads_status', '1')->count() >= $max) {
                return $this->message('Maksimal '.$max.' iklan untuk penempatan ini.', 422);
            }
            $data['ads_position'] = $newPosition;
            $data['ads_urutan'] = $this->nextUrutan($newPosition);
        }

        if ($isAdmob) {
            $request->validate(['admob_unit' => 'required|string|max:191']);

            // Beralih dari gambar ke AdMob: buang gambar lama.
            if (! $currentIsAdmob) {
                $this->removeFile((string) $ads->ads_url);
            }

            $data['ads_kind'] = $adsKind;
            $data['ads_admob_unit'] = (string) $request->input('admob_unit');
            $data['ads_url'] = '';
            $data['ads_link'] = '';
        } else {
            $data['ads_kind'] = 0;
            $data['ads_admob_unit'] = null;
            $data['ads_link'] = (string) $request->input('link', '');

            if ($request->hasFile('image')) {
                $request->validate(['image' => 'image']);
                $old = (string) $ads->ads_url;
                $data['ads_url'] = $this->storeUpload($request, 'image', 'ads', 'i');
                if (! $currentIsAdmob) {
                    $this->removeFile($old);
                }
            } elseif ($currentIsAdmob || empty($ads->ads_url)) {
                // Beralih ke gambar tanpa mengunggah berkas baru tidak valid.
                return $this->message('Gambar iklan wajib diunggah.', 422);
            }
        }

        if ($newPosition === self::POSISI_ANTAR_KATEGORI) {
            if ($request->has('category_id')) {
                $data['ads_category_id'] = $this->categoryId($request);
            }
        } else {
            // Pindah keluar dari penempatan antar-kategori: hapus pemasangan kategori.
            $data['ads_category_id'] = null;
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

    /** Format yang diminta; jatuh ke 'image' bila tidak dikenal. */
    private function normalizeKind(mixed $value): string
    {
        $kind = is_string($value) ? trim($value) : '';

        return array_key_exists($kind, self::KIND_TO_ADS) ? $kind : 'image';
    }

    /** Format full-screen memakai posisi tetap; format lain mengikuti form. */
    private function positionFor(string $kind, Request $request, ?int $fallback = null): int
    {
        if (isset(self::FULLSCREEN_POSITION[$kind])) {
            return self::FULLSCREEN_POSITION[$kind];
        }

        if ($request->filled('position')) {
            return (int) $request->input('position');
        }

        return $fallback ?? self::POSISI_ANTAR_KATEGORI;
    }

    /** Urutan berikutnya (di akhir) untuk sebuah penempatan. */
    private function nextUrutan(int $position): int
    {
        return ((int) Ads::where('ads_position', $position)->max('ads_urutan')) + 1;
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
