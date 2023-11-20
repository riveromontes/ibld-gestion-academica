<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'apellido'=>'required|alpha',
            'direccion'=>'required',
            'telefono'=>'required',
            'cedula'=>['required', Rule::unique('persons')->ignore($this->route('user')->person->id)],
            'email'=>['required', Rule::unique('users')->ignore($this->route('user')->id)]
        ];
    }
}
