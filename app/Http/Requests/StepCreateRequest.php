<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StepCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'indice' => 'required|numeric',
            'indicaciones' => 'nullable|string|max:1000',
            'id_recurso' => 'required|numeric',
            'tipo_recurso' => 'required|string|in:accommodation,cave,cultural,event,fair,locality,museum,natural,restaurant'
        ];
    }
}
