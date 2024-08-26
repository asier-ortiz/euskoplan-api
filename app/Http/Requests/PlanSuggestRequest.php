<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanSuggestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currentYear = date('Y');
        return [
            'provincia' => 'required|string|in:Araba/Álava,Bizkaia,Gipuzkoa',
            'dias' => 'required|integer|min:1|max:3',
            'tipo_viaje' => 'required|string|in:cultura,aventura,familiar',
            'mes' => 'required|integer|min:1|max:12', // Mes como un entero entre 1 y 12
            'año' => 'required|integer|min:' . $currentYear . '|max:' . ($currentYear + 1) // Año como un entero, entre el año actual y el siguiente
        ];
    }
}

