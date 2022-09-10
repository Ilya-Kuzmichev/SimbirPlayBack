<?php

namespace models;

use Illuminate\Database\Eloquent\Model;

class Purchases extends Model
{
    protected $table = 'purchases';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = ['user_id', 'merch_id', 'price', 'address'];
}