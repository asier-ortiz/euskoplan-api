<?php

namespace App\Http\Controllers;

use App\Http\Resources\CaveCompactResource;
use App\Http\Resources\CaveResource;
use App\Models\Cave;
use App\Traits\HasCategories;
use App\Traits\HasFilter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CaveController extends Controller
{
    use HasCategories, HasFilter;

    public function show($code, $language): CaveResource
    {
        $cave = Cave::where('codigo', '=', $code)->where('idioma', '=', $language)->firstOrFail();
        return new CaveResource($cave);
    }

    protected function getModel(): string
    {
        return Cave::class;
    }

    protected function getResourceClass(): string
    {
        return CaveCompactResource::class;
    }
}
