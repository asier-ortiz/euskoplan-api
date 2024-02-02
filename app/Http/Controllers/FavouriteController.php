<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavouriteCreateRequest;
use App\Http\Resources\FavouriteResource;
use App\Models\Favourite;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class FavouriteController extends Controller
{

    public function show(): FavouriteResource
    {
        return new FavouriteResource(request()->user());
    }

    public function store(FavouriteCreateRequest $request): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $validatedData = $request->validated();

        $favouriteExists = Favourite::where('user_id', '=', $request->user()->id)
            ->where('favouritables_id', '=', $validatedData['id_favorito'])
            ->where('favouritables_type', '=', $validatedData['tipo_favorito'])
            ->first();

        if ($favouriteExists)
            return response(['error' => 'Resource already saved in favorites'], Response::HTTP_BAD_REQUEST);

        $favourite = Favourite::create([
            'user_id' => request()->user()->id,
            'favouritables_id' => $validatedData['id_favorito'],
            'favouritables_type' => $validatedData['tipo_favorito'],
        ]);

        return response(new FavouriteResource($favourite->user), Response::HTTP_ACCEPTED);
    }

    public function destroy($id): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $favourite = Favourite::find($id);
        if (!Gate::allows('destroy-favourite', $favourite)) abort(Response::HTTP_FORBIDDEN);
        Favourite::destroy($id);
        return response(new FavouriteResource(request()->user()), Response::HTTP_ACCEPTED);
    }

}
