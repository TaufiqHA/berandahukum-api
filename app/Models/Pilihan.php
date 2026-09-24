<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pilihan extends Model
{
    protected $table = 'tbl_pilihan';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}
