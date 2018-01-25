<?php

namespace App\Api\V1\Requests;



class UserRequest extends BaseRequest
{
    public function rules()
    {
        $id = \Auth::user()->id;

        return [
          'name' => 'required|max:100',
          'username' => 'required|max:25|unique:users,username,' . $id,
          'email' => 'required|max:100|unique:users,email,' . $id,
          'gender_id' => 'integer|between:1,3',
        ];
    }
}