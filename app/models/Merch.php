<?php

namespace models;

use Illuminate\Database\Eloquent\Model;

class Merch extends Model
{
    protected $table = 'merch';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = ['name', 'price', 'picture'];
}