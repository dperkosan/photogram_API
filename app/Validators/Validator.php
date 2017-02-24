<?php

namespace App\Validators;


use Illuminate\Http\Request as Request;

abstract class Validator
{
    public static $rules;
    public $errors;
    protected $data;
    public static $messages;

    public function __construct($data = null, Request $request)
    {
        $this->data = $data ?: $request->all();
    }

    public function passes()
    {
        $validation = \Validator::make($this->data, static::$rules, static::$messages);
        if($validation->passes()) return true;
        $this->errors = $validation->messages();
        return false;
    }

}