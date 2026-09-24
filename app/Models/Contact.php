<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'tbl_contact';
    protected $primaryKey = 'contact_id';
    public $timestamps = false;
    protected $guarded = [];
}
