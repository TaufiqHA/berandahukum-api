<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pertanyaan;
use App\Models\SysSetting;
use Illuminate\Http\Request;

class PertanyaanController extends Controller
{
    public function index()
    {
        return view('admin.pertanyaan.index', ['title' => 'Daftar Pertanyaan']);
    }

    public function data()
    {
        return response()->json(['pertanyaan' => Pertanyaan::orderBy('pertanyaan_status')->orderByDesc('pertanyaan_date')->get()]);
    }

    public function jawab(Request $request, $id)
    {
        $pertanyaan = Pertanyaan::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate(['pertanyaan_jawab' => 'required']);
            $pertanyaan->update([
                'pertanyaan_jawaban' => $request->input('pertanyaan_jawab'),
                'pertanyaan_status' => 1,
            ]);

            return redirect(site_admin('pertanyaan'))->with('msg_flash', success_message('Jawaban berhasil disimpan.'));
        }

        return view('admin.pertanyaan.jawab', ['title' => 'Jawab Pertanyaan', 'pertanyaan' => $pertanyaan]);
    }

    public function destroy($id)
    {
        Pertanyaan::findOrFail($id)->delete();

        return redirect(site_admin('pertanyaan'))->with('msg_flash', success_message('Pertanyaan berhasil dihapus.'));
    }

    public function updateStatus(Request $request)
    {
        SysSetting::query()->update(['show_pertanyaan' => $request->input('status')]);

        return response()->json(['message' => success_message('Status Tampil berhasil diUpdate.')]);
    }
}
