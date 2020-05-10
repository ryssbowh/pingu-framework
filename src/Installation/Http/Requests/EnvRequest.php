<?php

namespace Pingu\Installation\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use PinguInstaller\Components\DatabaseChecker;
use PinguInstaller\Exceptions\DriverNotInstalled;

class EnvRequest extends FormRequest
{
	/**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $rules = [
        	'DB_CONNECTION' => 'required|in:'.implode(',', array_keys(config('installer.drivers'))), 
            'DB_HOST' => 'required', 
            'DB_DATABASE' => 'required', 
            'DB_USERNAME' => 'required', 
            'DB_PASSWORD' => 'required'
        ];
        foreach(config('installer.env') as $name => $data){
        	$rules[$name] = $data['validation'] ?? '';
        }
        return $rules;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {   
        $messages = [
        	'DB_CONNECTION.required' => 'Database driver is required',
        	'DB_CONNECTION.in' => 'Database driver is invalid',
            'DB_HOST.required' => 'Database host is required', 
            'DB_DATABASE.required' => 'Database name is required', 
            'DB_USERNAME.required' => 'Database username is required', 
            'DB_PASSWORD.required' => 'Database password is required'
        ];
        foreach(config('installer.env') as $name => $data){
        	$messages = array_merge($messages, $data['messages'] ?? []);
        }
        return $messages;
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
        	$validated = $validator->validated();
            try{
                DatabaseChecker::checkConnection($validated['DB_CONNECTION'], $validated['DB_HOST'], $validated['DB_DATABASE'], $validated['DB_USERNAME'], $validated['DB_PASSWORD']);
            }
            catch(DriverNotInstalled $e){
            	$validator->errors()->add('DB_NAME', $e->getMessage());
            }
            catch(\ErrorException $e){
                $validator->errors()->add('DB_NAME', 'Cannot connect to database with these credentials');
            }
        });
    }   
}