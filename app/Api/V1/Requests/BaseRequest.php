<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest as DingoRequest;

class BaseRequest extends DingoRequest
{
    protected $configName;

    public function rules()
    {
        return Config::get('validations.' . $this->getConfigName());
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
