<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'tbl_user';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
    protected $guarded = [];
    protected $hidden = ['user_password'];

    public function getAuthPassword()
    {
        return $this->user_password;
    }

    public function isAdmin(): bool
    {
        return $this->user_level === 'admin';
    }
}
