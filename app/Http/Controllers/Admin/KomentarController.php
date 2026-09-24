<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;

class KomentarController extends Controller
{
    public function index()
    {
        return view('admin.komentar.index', ['title' => 'Daftar Komentar']);
    }

    public function data()
    {
        $rows = Comment::orderBy('comment_status')->orderByDesc('comment_date')->get()->map(function ($c) {
            $c = $c->toArray();
            $c['article_title'] = Article::find($c['article_id'])->article_title ?? '(artikel terhapus)';

            return $c;
        });

        return response()->json(['comment' => $rows]);
    }

    public function reply(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate(['comment_reply' => 'required']);
            $comment->update(['comment_reply' => $request->input('comment_reply')]);

            return redirect(site_admin('komentar'))->with('msg_flash', success_message('Jawaban berhasil disimpan.'));
        }

        return view('admin.komentar.reply', ['title' => 'Balas Komentar', 'comment' => $comment]);
    }

    public function destroy($id)
    {
        Comment::findOrFail($id)->delete();

        return redirect(site_admin('komentar'))->with('msg_flash', success_message('Komentar berhasil dihapus.'));
    }

    public function publish($id)
    {
        Comment::findOrFail($id)->update(['comment_status' => '1']);

        return redirect(site_admin('komentar'))->with('msg_flash', success_message('Komentar berhasil diupdate dan ditampilkan.'));
    }

    public function unpublish($id)
    {
        Comment::findOrFail($id)->update(['comment_status' => '0']);

        return redirect(site_admin('komentar'))->with('msg_flash', success_message('Komentar berhasil diupdate dan TIDAK ditampilkan.'));
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('koment', []);
        $berhasil = 0;
        foreach ($ids as $id) {
            if (Comment::find($id)?->delete()) {
                $berhasil++;
            }
        }

        return response()->json([
            'value' => $berhasil === count($ids) ? '1' : '0',
            'message' => success_message($berhasil.' comment berhasil dihapus.'),
        ]);
    }
}
