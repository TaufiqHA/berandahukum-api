<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\Request;

class QuotesController extends Controller
{
    public function index()
    {
        return view('admin.quotes.index', ['title' => 'Daftar Quote', 'quotes' => Quote::orderBy('urutan')->get()]);
    }

    public function create()
    {
        return view('admin.quotes.form', ['title' => 'Tambah Quote', 'row' => null]);
    }

    public function edit($id)
    {
        return view('admin.quotes.form', ['title' => 'Ubah Quote', 'row' => Quote::findOrFail($this->decrypt($id))]);
    }

    public function store(Request $request)
    {
        Quote::create($this->payload($request));

        return redirect(site_admin('quotes'))->with('msg_flash', success_message('Data quote berhasil disimpan.'));
    }

    public function update(Request $request, $id)
    {
        Quote::findOrFail($this->decrypt($id))->update($this->payload($request));

        return redirect(site_admin('quotes'))->with('msg_flash', success_message('Data quote berhasil disimpan.'));
    }

    public function destroy($id)
    {
        Quote::findOrFail($this->decrypt($id))->delete();

        return redirect(site_admin('quotes'))->with('msg_flash', success_message('Berhasil dihapus.'));
    }

    private function payload(Request $request): array
    {
        $request->validate(['urutan' => 'required']);

        $file = '';
        if ($request->hasFile('quote_image')) {
            $f = $request->file('quote_image');
            $file = 'quote_'.kode_unik().'_'.date('ymdHis').'.'.$f->getClientOriginalExtension();
            $f->move(public_path('uploads/img'), $file);
        }

        return [
            'quote_image' => $file,
            'urutan' => (int) $request->input('urutan'),
            'quote_status' => $request->input('quoteStatus') === 'yes' ? '1' : '0',
        ];
    }

    private function decrypt(string $id): int
    {
        if (ctype_digit($id)) {
            return (int) $id;
        }
        foreach (Quote::pluck('quote_id') as $qid) {
            if (md5((string) $qid) === $id) {
                return (int) $qid;
            }
        }
        abort(404);
    }
}
