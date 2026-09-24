<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsPop extends Model
{
    protected $table = 'tbl_ads_pop';
    protected $primaryKey = 'ads_id';
    public $timestamps = false;
    protected $guarded = [];
}
