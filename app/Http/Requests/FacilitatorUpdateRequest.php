<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class FacilitatorUpdateRequest extends FormRequest
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
            'nombre'=>'required|alpha',
            'apellido'=>'required|alpha',
            'direccion'=>'required',
            'telefono'=>'required',
            'correo'=>['required', Rule::unique('students')->ignore($this->route('facilitator')->id)],
            'cedula'=>['required', Rule::unique('students')->ignore($this->route('facilitator')->id)]
        ];
    }
}
