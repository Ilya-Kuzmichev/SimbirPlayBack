<?php

namespace models;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;

class Departament extends Model
{
    protected $table = 'departament';

    public static function getList()
    {
        $result = [];
        $departaments = Manager::table((new self())->getTable())->get(['id', 'name'])->all();
        foreach ($departaments as $departament) {
            $result[$departament->id] = $departament->name;
        }
        return $result;
    }
}