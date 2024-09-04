<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idioma' => 'required|string|min:2|max:2|in:es,eu,en',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'publico' => 'nullable|boolean',
            'pasos' => 'present|array',
            'pasos.*.indice' => 'required|numeric',
            'pasos.*.dia' => 'required|numeric',
            'pasos.*.indicaciones' => 'nullable|string|max:1000',
            'pasos.*.id_recurso' => 'required|numeric',
            'pasos.*.tipo_recurso' => 'required|string'
        ];
    }
}
