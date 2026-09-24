<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ArticleController;
use Illuminate\Http\Request;

class SearchController extends BaseApiController
{
    public function index(Request $request, ArticleController $articles)
    {
        return $articles->index($request);
    }
}
