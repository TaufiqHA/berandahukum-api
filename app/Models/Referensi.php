<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referensi extends Model
{
    protected $table = 'tbl_referensi';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}
