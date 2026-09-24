<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Pertanyaan;
use Illuminate\Http\Request;

class QuestionController extends AdminApiController
{
    public function index(Request $request)
    {
        $query = Pertanyaan::query()->orderBy('pertanyaan_status')->orderByDesc('pertanyaan_date');

        if ($request->filled('status')) {
            $query->where('pertanyaan_status', (int) $request->input('status'));
        }

        $paginator = $query->paginate((int) $request->input('per_page', 15))
            ->through(fn ($p) => [
                'id' => (int) $p->pertanyaan_id,
                'date' => $p->pertanyaan_date,
                'name' => $p->pertanyaan_nama,
                'email' => $p->pertanyaan_email,
                'question' => $p->pertanyaan,
                'answer' => $p->pertanyaan_jawaban,
                'status' => (int) $p->pertanyaan_status,
            ]);

        return $this->ok([
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
        ]);
    }

    public function answer(Request $request, $id)
    {
        $request->validate(['answer' => 'required|string']);
        Pertanyaan::findOrFail($id)->update([
            'pertanyaan_jawaban' => $request->input('answer'),
            'pertanyaan_status' => 1,
        ]);

        return $this->message('Jawaban berhasil disimpan.');
    }

    public function destroy($id)
    {
        Pertanyaan::findOrFail($id)->delete();

        return $this->message('Pertanyaan dihapus.');
    }
}
