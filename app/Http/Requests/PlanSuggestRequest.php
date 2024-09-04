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
            'año' => 'required|integer|min:' . $currentYear . '|max:' . ($currentYear + 1), // Año como un entero, entre el año actual y el siguiente
            'idioma' => 'required|string|in:es,eu,en', // Idioma debe ser es, eu, o en
        ];
    }

    public function messages(): array
    {
        return [
            'provincia.in' => 'La provincia debe ser una de las siguientes: Araba/Álava, Bizkaia, Gipuzkoa.',
            'dias.min' => 'El número mínimo de días es 1.',
            'dias.max' => 'El número máximo de días es 3.',
            'tipo_viaje.in' => 'El tipo de viaje debe ser una de las siguientes opciones: cultura, aventura, familiar.',
            'mes.min' => 'El mes debe ser un valor entre 1 y 12.',
            'mes.max' => 'El mes debe ser un valor entre 1 y 12.',
            'año.min' => 'El año no puede ser anterior al año actual.',
            'año.max' => 'El año no puede ser posterior al siguiente año.',
            'idioma.in' => 'El idioma debe ser uno de los siguientes: es (español), eu (euskera), en (inglés).',
        ];
    }
}
