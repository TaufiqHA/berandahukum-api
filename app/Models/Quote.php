<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $table = 'tbl_quotes';
    protected $primaryKey = 'quote_id';
    public $timestamps = false;
    protected $guarded = [];
}
