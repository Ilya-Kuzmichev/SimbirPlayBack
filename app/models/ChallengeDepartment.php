<?php

namespace models;

use Illuminate\Database\Eloquent\Model;

class ChallengeDepartment extends Model
{
    protected $table = 'challenge_department';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = ['challenge_id', 'department_id'];
}