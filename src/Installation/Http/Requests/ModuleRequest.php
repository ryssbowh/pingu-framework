<?php

namespace Pingu\Installation\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use PinguInstaller\Components\DatabaseChecker;

class ModuleRequest extends FormRequest
{
	/**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        return ['modules' => 'sometimes|array'];
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

    /**
     * Checks that modules in post exist on disk
     * 
     * @param  Validator $validator
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(
            function (Validator $validator) {
                $validated = $validator->validated();
                $modules = array_map(
                    function ($module) {
                        return $module->getName();
                    }, \Module::all()
                );
                foreach ($validated['modules'] ?? [] as $name) {
                    if (!in_array($name, $modules)) {
                        $validator->errors()->add($name, $name.' is not a valid module');
                    }
                }
            }
        );
    }   
}