<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    protected $table = 'tbl_pertanyaan';
    protected $primaryKey = 'pertanyaan_id';
    public $timestamps = false;
    protected $guarded = [];
}
