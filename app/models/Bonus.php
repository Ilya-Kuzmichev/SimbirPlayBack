<?php

namespace models;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    protected $table = 'bonus';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = ['user_id', 'giver_id', 'responsible_id'];
}