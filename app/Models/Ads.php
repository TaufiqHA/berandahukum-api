<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    protected $table = 'tbl_ads';
    protected $primaryKey = 'ads_id';
    public $timestamps = false;
    protected $guarded = [];
}
