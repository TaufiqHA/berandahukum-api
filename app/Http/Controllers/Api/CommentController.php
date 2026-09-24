<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends BaseApiController
{
    public function index(int $articleId)
    {
        return response()->json(Comment::where('article_id', $articleId)
            ->where('comment_status', '1')->orderByDesc('comment_date')->get()
            ->map(fn ($c) => [
                'name' => $c->comment_name,
                'fill' => $c->comment_fill,
                'reply' => $c->comment_reply,
                'date' => $c->comment_date,
            ])->all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'article_id' => 'required|integer',
            'name' => 'required|max:100',
            'fill' => 'required',
        ]);

        Comment::create([
            'article_id' => $data['article_id'],
            'comment_name' => $data['name'],
            'comment_fill' => $data['fill'],
            'comment_status' => '0',
        ]);

        return response()->json(['message' => 'Komentar terkirim dan menunggu verifikasi.'], 201);
    }
}
