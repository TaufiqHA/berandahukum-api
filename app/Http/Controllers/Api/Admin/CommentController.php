<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends AdminApiController
{
    public function index(Request $request)
    {
        $query = Comment::query()->orderBy('comment_status')->orderByDesc('comment_date');

        if ($request->filled('status')) {
            $query->where('comment_status', (int) $request->input('status'));
        }
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(fn ($w) => $w->where('comment_name', 'like', '%'.$q.'%')->orWhere('comment_fill', 'like', '%'.$q.'%'));
        }

        $paginator = $query->paginate((int) $request->input('per_page', 15))
            ->through(function ($c) {
                return [
                    'id' => (int) $c->comment_id,
                    'article_id' => (int) $c->article_id,
                    'article_title' => Article::find($c->article_id)->article_title ?? '(artikel terhapus)',
                    'name' => $c->comment_name,
                    'fill' => $c->comment_fill,
                    'reply' => $c->comment_reply,
                    'status' => (int) $c->comment_status,
                    'date' => $c->comment_date,
                ];
            });

        return $this->ok([
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['reply' => 'required|string']);
        Comment::findOrFail($id)->update(['comment_reply' => $request->input('reply')]);

        return $this->message('Jawaban berhasil disimpan.');
    }

    public function publish($id)
    {
        Comment::findOrFail($id)->update(['comment_status' => 1]);

        return $this->message('Komentar ditampilkan.');
    }

    public function unpublish($id)
    {
        Comment::findOrFail($id)->update(['comment_status' => 0]);

        return $this->message('Komentar disembunyikan.');
    }

    public function destroy($id)
    {
        Comment::findOrFail($id)->delete();

        return $this->message('Komentar dihapus.');
    }
}
