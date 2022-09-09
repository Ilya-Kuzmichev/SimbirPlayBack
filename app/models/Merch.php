<?php

namespace models;

use Illuminate\Database\Eloquent\Model;

class Merch extends Model
{
    protected $table = 'merch';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = ['name', 'price'];

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|max:255',
            'email'    => 'required|max:255|unique:users|exists:emails',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['string'],
            'price' => ['integer'],
        ];
    }
}