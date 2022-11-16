<?php

namespace models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $table = 'achievement';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = ['name', 'default_rating'];
}