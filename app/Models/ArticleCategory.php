<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    protected $table = 'tbl_article_category';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}
