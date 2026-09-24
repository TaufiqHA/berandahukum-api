<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    protected $table = 'tbl_label';
    protected $primaryKey = 'label_id';
    public $timestamps = false;
    protected $guarded = [];
}
