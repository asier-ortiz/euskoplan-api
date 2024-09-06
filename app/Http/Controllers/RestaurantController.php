<?php

namespace App\Http\Controllers;

use App\Http\Resources\RestaurantCompactResource;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use App\Traits\HasCategories;
use App\Traits\HasFilter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RestaurantController extends Controller
{
    use HasCategories, HasFilter;

    public function show($code, $language): RestaurantResource
    {
        $restaurant = Restaurant::where('codigo', '=', $code)->where('idioma', '=', $language)->firstOrFail();
        return new RestaurantResource($restaurant);
    }

    protected function getModel(): string
    {
        return Restaurant::class;
    }

    protected function getResourceClass(): string
    {
        return RestaurantCompactResource::class;
    }
}
