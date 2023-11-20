<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StudentUpdateRequest extends FormRequest
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
            'nombre'=>'nullable|alpha',
            'apellido'=>'nullable|alpha',
            'direccion'=>'nullable',
            'telefono'=>'nullable',
            'correo'=>['nullable', Rule::unique('students')->ignore($this->route('student')->id)],
            'cedula'=>['nullable', Rule::unique('students')->ignore($this->route('student')->id)]
        ];
    }
}
