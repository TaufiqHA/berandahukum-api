<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Label;
use Illuminate\Http\Request;

class LabelController extends AdminApiController
{
    public function index()
    {
        $rows = Label::orderBy('urutan')->orderBy('label_id')->get()->map(fn ($l) => [
            'id' => (int) $l->label_id,
            'name' => $l->label_name,
            'uri' => $l->label_uri,
            'show' => $l->label_show === 'yes',
            'urutan' => (int) $l->urutan,
        ])->all();

        return $this->ok(['data' => $rows]);
    }

    public function store(Request $request)
    {
        Label::create($this->payload($request));

        return $this->message('Label berhasil disimpan.', 201);
    }

    public function update(Request $request, $id)
    {
        Label::findOrFail($id)->update($this->payload($request));

        return $this->message('Label berhasil diubah.');
    }

    public function destroy($id)
    {
        Label::findOrFail($id)->delete();

        return $this->message('Label berhasil dihapus.');
    }

    private function payload(Request $request): array
    {
        $request->validate(['name' => 'required|string|max:50']);
        $name = $request->input('name');

        return [
            'label_uri' => urlencode(str_replace(' ', '-', strtolower($name))),
            'label_name' => $name,
            'label_show' => $request->boolean('show') ? 'yes' : 'no',
            'urutan' => (int) $request->input('urutan', 0),
        ];
    }
}
