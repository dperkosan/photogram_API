<?php

namespace App\Api\V1\Requests;

use Dingo\Api\Http\FormRequest as DingoRequest;

class BaseRequest extends DingoRequest
{
    protected $configName;

    public function rules()
    {
        return config('validations.' . $this->getConfigName());
    }

    public function authorize()
    {
        return true;
    }

    protected function getConfigName()
    {
        return $this->configName;
    }
}
