<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Label;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function index()
    {
        return view('admin.label.index', [
            'title' => 'Daftar Label',
            'label' => Label::orderBy('urutan')->orderBy('label_id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.label.form', ['title' => 'Tambah Label', 'row' => null]);
    }

    public function edit($id)
    {
        return view('admin.label.form', ['title' => 'Ubah Label', 'row' => Label::findOrFail($id)]);
    }

    public function store(Request $request)
    {
        $data = $this->payload($request);
        Label::create($data);

        return redirect(site_admin('label'))->with('msg_flash', success_message('Data berhasil disimpan.'));
    }

    public function update(Request $request, $id)
    {
        Label::findOrFail($id)->update($this->payload($request));

        return redirect(site_admin('label'))->with('msg_flash', success_message('Data berhasil diubah.'));
    }

    public function destroy($id)
    {
        Label::findOrFail($id)->delete();

        return redirect(site_admin('label'))->with('msg_flash', success_message('Label berhasil dihapus.'));
    }

    private function payload(Request $request): array
    {
        $request->validate(['labelName' => 'required|max:50']);
        $name = $request->input('labelName');

        return [
            'label_uri' => urlencode(str_replace(' ', '-', strtolower($name))),
            'label_name' => $name,
            'label_show' => $request->input('labelShow') === 'yes' ? 'yes' : 'no',
            'urutan' => (int) $request->input('urutan', 0),
        ];
    }
}
