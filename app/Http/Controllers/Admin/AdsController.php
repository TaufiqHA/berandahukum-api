<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    public function index()
    {
        return view('admin.ads.index', ['title' => 'Daftar Iklan', 'ads' => Ads::where('ads_status', '1')->orderByDesc('ads_id')->get()]);
    }

    public function create()
    {
        return view('admin.ads.form', ['title' => 'Tambah Iklan', 'row' => null]);
    }

    public function edit($id)
    {
        return view('admin.ads.form', ['title' => 'Ubah Iklan', 'row' => Ads::where('ads_id', '=', $this->decrypt($id))->firstOrFail()]);
    }

    public function store(Request $request)
    {
        Ads::create($this->payload($request));

        return redirect(site_admin('ads'))->with('msg_flash', success_message('Data iklan berhasil disimpan.'));
    }

    public function update(Request $request, $id)
    {
        $ads = Ads::findOrFail($this->decrypt($id));
        $ads->update($this->payload($request, $ads));

        return redirect(site_admin('ads'))->with('msg_flash', success_message('Data iklan berhasil disimpan.'));
    }

    public function destroy($id)
    {
        $ads = Ads::findOrFail($this->decrypt($id));
        $ads->update(['ads_status' => '0']);

        return redirect(site_admin('ads'))->with('msg_flash', success_message('Iklan berhasil dihapus.'));
    }

    private function payload(Request $request, ?Ads $existing = null): array
    {
        $request->validate(['adsPosition' => 'required', 'adsType' => 'required', 'adsFileType' => 'required']);

        $data = [
            'ads_position' => (int) $request->input('adsPosition'),
            'ads_type' => $request->input('adsType'),
            'ads_file_type' => $request->input('adsFileType'),
            'ads_link' => $request->input('ads_link'),
            'ads_status' => '1',
        ];

        if ($request->input('adsType') == '0') {
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
            $data['ads_url'] = $request->input('adsUrl');
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
