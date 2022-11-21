<?php

namespace models;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';

    public static function getList()
    {
        $result = [];
        $departments = Manager::table((new self())->getTable())->get(['id', 'name'])->all();
        foreach ($departments as $department) {
            $result[$department->id] = $department->name;
        }
        return $result;
    }
}