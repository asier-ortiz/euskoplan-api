<?php

namespace App\Http\Controllers;

use App\Http\Resources\CulturalCompactResource;
use App\Http\Resources\CulturalResource;
use App\Models\Cultural;
use App\Traits\HasCategories;
use App\Traits\HasFilter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CulturalController extends Controller
{
    use HasCategories, HasFilter;

    public function show($code, $language): CulturalResource
    {
        $cultural = Cultural::where('codigo', '=', $code)->where('idioma', '=', $language)->firstOrFail();
        return new CulturalResource($cultural);
    }

    protected function getModel(): string
    {
        return Cultural::class;
    }

    protected function getResourceClass(): string
    {
        return CulturalCompactResource::class;
    }
}
