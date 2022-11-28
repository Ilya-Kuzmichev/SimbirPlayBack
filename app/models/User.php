<?php

namespace models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    const ROLE_USER = 1;
    const ROLE_ADMIN = 2;

    protected $table = 'user';

    protected $fillable = ['login', 'share_achievement', 'share_rating'];
}