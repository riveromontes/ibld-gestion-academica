<?php

namespace App\Http\Requests\Inscription;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'nombre'=>'required',
            'apellido'=>'required',
            'cedula'=>'required|unique:persons',
            'direccion'=>'required',
            'telefono'=>'required',
            'correo'=>'required|unique:users,email',
            'autorizacion_pastoral' => 'required',
            'cedula_file' => 'required',
            'foto' => 'required',
            'estudios_cursados' => 'required',
            'planilla_ins' => 'required',
            'chair' => 'required|integer|exists:chair_module,id'
        ];
    }

    public function messages()
    {
        return [
            'correo.unique' => 'El correo ya esta en uso, debe contactar con algun administrador.',
            'cedula.unique' => 'La cedula ya esta en uso, debe contactar con algun administrador.',
        ];
}
}
