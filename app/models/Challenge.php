<?php

namespace models;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $table = 'challenge';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = ['name', 'description', 'start_date', 'end_date', 'budget', 'responsible_id'];
}