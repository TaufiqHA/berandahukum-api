<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Services\AdPositions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdsController extends Controller
{
    /**
     * Posisi minimum iklan khusus aplikasi mobile. Iklan pada posisi ini
     * (100–103) dikelola dari panel mobile dan tidak ditampilkan di website.
     */
    private const MOBILE_MIN_POSITION = 100;

    public function index()
    {
        return view('admin.ads.index', [
            'title' => 'Daftar Iklan',
            'ads' => $this->webAds()->where('ads_status', '1')->orderByDesc('ads_id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.ads.form', [
            'title' => 'Tambah Iklan',
            'row' => null,
            'positions' => AdPositions::web(),
        ]);
    }

    public function edit($id)
    {
        return view('admin.ads.form', [
            'title' => 'Ubah Iklan',
            'row' => $this->webAds()->where('ads_id', '=', $this->decrypt($id))->firstOrFail(),
            'positions' => AdPositions::web(),
        ]);
    }

    public function store(Request $request)
    {
        Ads::create($this->payload($request));

        return redirect(site_admin('ads'))->with('msg_flash', success_message('Data iklan berhasil disimpan.'));
    }

    public function update(Request $request, $id)
    {
        $ads = $this->webAds()->findOrFail($this->decrypt($id));
        $ads->update($this->payload($request, $ads));

        return redirect(site_admin('ads'))->with('msg_flash', success_message('Data iklan berhasil disimpan.'));
    }

    public function destroy($id)
    {
        $ads = $this->webAds()->findOrFail($this->decrypt($id));
        $ads->update(['ads_status' => '0']);

        return redirect(site_admin('ads'))->with('msg_flash', success_message('Iklan berhasil dihapus.'));
    }

    /** Dasar query iklan yang dikelola dari website: hanya posisi non-mobile. */
    private function webAds()
    {
        return Ads::where('ads_position', '<', self::MOBILE_MIN_POSITION);
    }

    private function payload(Request $request, ?Ads $existing = null): array
    {
        $isUpload = $request->input('adsType') === '0';

        $rules = [
            'adsPosition' => ['required', 'integer', Rule::in(array_keys(AdPositions::web()))],
            'adsType' => ['required', Rule::in(['0', '1'])],
            // Iklan unggahan (Gambar Upload) hanya boleh bertipe file Gambar.
            'adsFileType' => ['required', Rule::in($isUpload ? ['0'] : ['0', '1', '2'])],
            'ads_link' => ['nullable', 'string', 'max:191'],
        ];

        if ($isUpload) {
            $rules['adsFile'] = [
                Rule::requiredIf(fn () => ! $existing || (string) $existing->ads_url === ''),
                'image',
                'max:2048',
            ];
        } else {
            $rules['adsUrl'] = ['required', 'string'];
        }

        $messages = [
            'adsPosition.required' => 'Posisi iklan wajib diisi.',
            'adsPosition.integer' => 'Posisi iklan harus berupa angka.',
            'adsPosition.in' => 'Posisi iklan tidak tersedia. Pilih nomor dari daftar keterangan posisi.',
            'adsType.required' => 'Tipe iklan wajib dipilih.',
            'adsType.in' => 'Tipe iklan yang dipilih tidak valid.',
            'adsFileType.required' => 'Tipe file wajib dipilih.',
            'adsFileType.in' => 'Tipe file tidak sesuai dengan tipe iklan. Untuk Gambar Upload, pilih tipe file Gambar.',
            'adsFile.required' => 'Gambar iklan wajib diunggah.',
            'adsFile.image' => 'File yang diunggah harus berupa gambar (jpg, png, gif, webp, dll).',
            'adsFile.max' => 'Ukuran gambar maksimal 2 MB.',
            'adsUrl.required' => 'URL / kode embed wajib diisi.',
            'ads_link.max' => 'Link terlalu panjang (maksimal 191 karakter).',
        ];

        $request->validate($rules, $messages);

        $data = [
            'ads_position' => (int) $request->input('adsPosition'),
            'ads_type' => $request->input('adsType'),
            'ads_file_type' => $request->input('adsFileType'),
            'ads_link' => $request->input('ads_link') ?: '#',
            'ads_status' => '1',
        ];

        if ($isUpload) {
            if ($request->hasFile('adsFile')) {
                $file = $request->file('adsFile');
                $name = kode_unik().'_'.date('ymdHis').'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/i'), $name);
                $data['ads_url'] = $name;
            } elseif ($existing) {
                $data['ads_url'] = $existing->ads_url;
            } else {
                $data['ads_url'] = '';
            }
        } else {
            $data['ads_url'] = (string) $request->input('adsUrl');
        }

        return $data;
    }

    /** Terima id mentah atau md5(id) agar kompatibel dengan tautan lama. */
    private function decrypt(string $id): int
    {
        if (ctype_digit($id)) {
            return (int) $id;
        }

        foreach (Ads::pluck('ads_id') as $adsId) {
            if (md5((string) $adsId) === $id) {
                return (int) $adsId;
            }
        }

        abort(404);
    }
}
