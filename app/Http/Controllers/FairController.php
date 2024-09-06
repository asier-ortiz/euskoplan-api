<?php

namespace App\Http\Controllers;

use App\Http\Resources\FairCompactResource;
use App\Http\Resources\FairResource;
use App\Models\Fair;
use App\Traits\HasFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FairController extends Controller
{
    use HasFilter;

    public function show($code, $language): FairResource
    {
        $fair = Fair::where('codigo', '=', $code)->where('idioma', '=', $language)->firstOrFail();
        return new FairResource($fair);
    }

    protected function getModel(): string
    {
        return Fair::class;
    }

    protected function getResourceClass(): string
    {
        return FairCompactResource::class;
    }
}
