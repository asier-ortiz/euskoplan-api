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
        return [
            'provincia' => 'required|string|in:Álava,Bizkaia,Gipuzkoa',
            'dias' => 'required|integer|min:1|max:3',
            'tipo_viaje' => 'required|string|in:cultura,aventura,familiar',
            'fecha_inicio' => 'required|date',
        ];
    }
}

