<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentCreateRequest extends FormRequest
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
            'inscription_id'=>'required',
            'fecha_pago'=>['required', 'date'],
            'banco'=>'required',
            'monto'=>['required', 'numeric', 'min:0.01'],
            'referencia'=>['required', 'string', 'max:50']

        ];
    }
}