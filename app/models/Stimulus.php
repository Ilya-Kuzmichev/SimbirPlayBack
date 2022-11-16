<?php

namespace models;

use Illuminate\Database\Eloquent\Model;

class Stimulus extends Model
{
    protected $table = 'stimulus';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = ['user_id', 'giver_id', 'balls', 'comment'];
}