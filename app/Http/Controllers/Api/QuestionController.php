<?php

namespace App\Http\Controllers\Api;

use App\Models\Pertanyaan;
use Illuminate\Http\Request;

class QuestionController extends BaseApiController
{
    public function index(Request $request)
    {
        $paginator = Pertanyaan::where('pertanyaan_status', 1)
            ->orderByDesc('pertanyaan_date')
            ->paginate((int) $request->input('per_page', 15))
            ->through(fn ($p) => [
                'id' => (int) $p->pertanyaan_id,
                'name' => $p->pertanyaan_nama,
                'question' => $p->pertanyaan,
                'answer' => $p->pertanyaan_jawaban,
                'date' => $p->pertanyaan_date,
            ]);

        return response()->json($paginator);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|max:100',
            'question' => 'required',
        ]);

        $row = Pertanyaan::create([
            'pertanyaan_date' => date('Y-m-d H:i:s'),
            'pertanyaan_nama' => $data['name'],
            'pertanyaan_email' => $data['email'],
            'pertanyaan' => $data['question'],
            'pertanyaan_status' => 0,
        ]);

        return response()->json([
            'message' => 'Pertanyaan berhasil dikirim dan akan dijawab.',
            'id' => (int) $row->pertanyaan_id,
        ], 201);
    }
}
