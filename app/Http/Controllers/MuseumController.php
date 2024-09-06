<?php

namespace App\Http\Controllers;

use App\Http\Resources\MuseumCompactResource;
use App\Http\Resources\MuseumResource;
use App\Models\Museum;
use App\Traits\HasCategories;
use App\Traits\HasFilter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MuseumController extends Controller
{
    use HasCategories, HasFilter;

    public function show($code, $language): MuseumResource
    {
        $museum = Museum::where('codigo', '=', $code)->where('idioma', '=', $language)->firstOrFail();
        return new MuseumResource($museum);
    }

    protected function getModel(): string
    {
        return Museum::class;
    }

    protected function getResourceClass(): string
    {
        return MuseumCompactResource::class;
    }
}
