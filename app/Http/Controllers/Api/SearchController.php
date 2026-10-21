<?php

namespace App\Http\Controllers\Api;

use App\Services\ArticleSearch;
use Illuminate\Http\Request;

class SearchController extends BaseApiController
{
    public function index(Request $request, ArticleController $articles, ArticleSearch $search)
    {
        return $articles->index($request, $search);
    }
}
