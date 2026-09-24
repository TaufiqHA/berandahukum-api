<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $perKategori = DB::table('tbl_article_category as ac')
            ->join('tbl_category as c', 'ac.category_id', '=', 'c.category_id')
            ->join('tbl_article as a', 'ac.article_id', '=', 'a.article_id')
            ->where('a.article_status', '!=', 0)
            ->groupBy('c.category_id', 'c.category_name')
            ->select('c.category_name', DB::raw('count(*) as jumlah'))
            ->get();

        $perPenulis = DB::table('tbl_article as a')
            ->leftJoin('tbl_user as u', 'u.user_id', '=', 'a.article_created_by')
            ->where('a.article_status', '!=', 0)
            ->groupBy('a.article_created_by', 'u.user_name')
            ->select(DB::raw("ifnull(u.user_name,'Admin') as penulis"), DB::raw('count(*) as jumlah'))
            ->get();

        return view('admin.dashboard', [
            'title' => 'Dashboard',
            'total_artikel' => Article::where('article_status', '!=', 0)->count(),
            'total_draft' => Article::where('article_status', 2)->count(),
            'total_publish' => Article::where('article_status', 1)->count(),
            'total_komentar' => Comment::count(),
            'total_komentar_nonver' => Comment::where('comment_status', 0)->count(),
            'total_komentar_ver' => Comment::where('comment_status', 1)->count(),
            'artikelkategori' => $perKategori,
            'artikelpenulis' => $perPenulis,
        ]);
    }
}
