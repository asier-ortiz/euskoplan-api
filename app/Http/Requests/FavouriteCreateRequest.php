<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FavouriteCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_favorito' => 'required|numeric',
            'tipo_favorito' => 'required|string|in:accommodation,cave,cultural,event,fair,locality,museum,natural,restaurant,plan'
        ];
    }
}
