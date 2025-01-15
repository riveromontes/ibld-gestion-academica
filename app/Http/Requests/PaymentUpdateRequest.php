<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentUpdateRequest extends FormRequest
{
    public function authorize()
    {
        // Cambiar a true si quieres permitir el acceso a todos los usuarios
        return true;
    }

    public function rules()
    {
        return [
            'inscription_id' => ['required', 'exists:inscriptions,id'],
            'fecha_pago' => ['required', 'date'],
            'banco' => ['required', 'alpha'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'referencia' => ['required', 'string', 'max:50']
        ];
    }
}