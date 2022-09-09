<?php

namespace models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $table = 'promo';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = ['name', 'default_rating'];
}