<?php

namespace models;

use Illuminate\Database\Eloquent\Model;

class AchievementGroup extends Model
{
    protected $table = 'achievement_group';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = ['name', 'image'];
}