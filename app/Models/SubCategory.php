<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = 'tbl_sub_category';
    protected $primaryKey = 'sub_category_id';
    public $timestamps = false;
    protected $guarded = [];
}
