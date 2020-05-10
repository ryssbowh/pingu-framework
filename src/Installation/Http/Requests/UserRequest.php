<?php

namespace Pingu\Installation\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use PinguInstaller\Components\DatabaseChecker;
use PinguInstaller\Exceptions\DriverNotInstalled;

class UserRequest extends FormRequest
{
	/**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        return [
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
            'repeat_password' => 'required|same:password'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {   
        return [];
    }
}