<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccommodationResource;
use App\Http\Resources\AccommodationCompactResource;
use App\Models\Accommodation;
use App\Traits\HasCategories;
use App\Traits\HasFilter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AccommodationController extends Controller
{
    use HasCategories, HasFilter;

    public function show($code, $language): AccommodationResource
    {
        $accommodation = Accommodation::where('codigo', '=', $code)->where('idioma', '=', $language)->firstOrFail();
        return new AccommodationResource($accommodation);
    }

    protected function getModel(): string
    {
        return Accommodation::class;
    }

    protected function getResourceClass(): string
    {
        return AccommodationCompactResource::class;
    }
}
