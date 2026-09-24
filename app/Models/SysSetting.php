<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SysSetting extends Model
{
    protected $table = 'sys_settings_m';
    protected $primaryKey = 'setting_id';
    public $timestamps = false;
    protected $guarded = [];
}
