<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'tbl_comment';
    protected $primaryKey = 'comment_id';
    public $timestamps = false;
    protected $guarded = [];
}
