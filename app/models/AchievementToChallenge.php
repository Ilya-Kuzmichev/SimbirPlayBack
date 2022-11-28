<?php

namespace models;

use Illuminate\Database\Eloquent\Model;

class AchievementToChallenge extends Model
{
    protected $table = 'achievement_to_challenge';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = ['achievement_id', 'challenge_id'];
}