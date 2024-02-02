<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanCreateRequest;
use App\Http\Requests\PlanUpdateRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Builder;

class PlanController extends Controller
{

    public function index(): AnonymousResourceCollection
    {
        return PlanResource::collection(
            Plan::query()
                ->when(request('idioma'), function (Builder $query) {
                    $query->where('idioma', '=', request('idioma'));
                })
                ->when(request('titulo'), function (Builder $query) {
                    $query->where('titulo', 'like', '%' . request('titulo') . '%');
                })
                ->when(request('descripcion'), function (Builder $query) {
                    $query->where('descripcion', 'like', '%' . request('descripcion') . '%');
                })
                ->when(request('id_usuario'), function (Builder $query) {
                    $query->where('user_id', '=', request('id_usuario'));
                })
                ->when(request('limite'), function (Builder $query) {
                    $query->take(request('limite'));
                })
                ->orderBy('votos', 'desc')
                ->where('publico', '=', true)
                ->get());
    }

    public function search(): AnonymousResourceCollection
    {
        $terms = explode(" ", request('busqueda'));

        return PlanResource::collection(
            Plan::query()
                ->where(function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('titulo', 'like', '%' . $term . '%');
                    }
                })
                ->orWhere(function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('descripcion', 'like', '%' . $term . '%');
                    }
                })
                ->orWhereHas('accommodations', function (Builder $query) use ($terms) {
                    $query->where('indicaciones', 'like', '%' . request('busqueda') . '%');
                })
                ->orWhereHas('caves', function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('indicaciones', 'like', '%' . $term . '%');
                    }
                })
                ->orWhereHas('culturals', function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('indicaciones', 'like', '%' . $term . '%');
                    }
                })
                ->orWhereHas('events', function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('indicaciones', 'like', '%' . $term . '%');
                    }
                })
                ->orWhereHas('fairs', function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('indicaciones', 'like', '%' . $term . '%');
                    }
                })
                ->orWhereHas('localities', function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('indicaciones', 'like', '%' . $term . '%');
                    }
                })
                ->orWhereHas('museums', function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('indicaciones', 'like', '%' . $term . '%');
                    }
                })
                ->orWhereHas('naturals', function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('indicaciones', 'like', '%' . $term . '%');
                    }
                })
                ->orWhereHas('restaurants', function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('indicaciones', 'like', '%' . $term . '%');
                    }
                })
                ->where('idioma', '=', request('idioma'))
                ->where('publico', '=', true)
                ->get());
    }

    public function userPlans(): AnonymousResourceCollection
    {
        $user = request()->user();
        return PlanResource::collection(Plan::where('user_id', '=', $user->id)->get());
    }

    public function show($id): \Illuminate\Http\Response|PlanResource|Application|ResponseFactory
    {
        $plan = Plan::find($id);
        if (!Gate::allows('read-plan', $plan)) return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        return new PlanResource(Plan::find($id));
    }

    public function store(PlanCreateRequest $request): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $validatedData = $request->validated();

        $plan = Plan::create([
            'idioma' => $validatedData['idioma'],
            'titulo' => $validatedData['titulo'],
            'descripcion' => $validatedData['descripcion'],
            'publico' => $validatedData['publico'],
            'user_id' => request()->user()->id
        ]);

        $steps = [];
        foreach ($validatedData['pasos'] as $step) {
            $steps[] = [
                'indice' => $step['indice'],
                'indicaciones' => $step['indicaciones'],
                'plan_id' => $plan->id,
                'planables_id' => $step['id_recurso'],
                'planables_type' => $step['tipo_recurso']
            ];
        }

        StepController::bulkStore($steps);
        return response(new PlanResource($plan), Response::HTTP_CREATED);
    }

    public function update(PlanUpdateRequest $request, $id): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $plan = Plan::find($id);
        if (!Gate::allows('update-plan', $plan)) return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        $validatedData = $request->validated();
        $plan->update(
            [
                'titulo' => $validatedData['titulo'],
                'descripcion' => $validatedData['descripcion'],
                'publico' => $validatedData['publico']
            ]);
        return response(new PlanResource($plan), Response::HTTP_ACCEPTED);
    }

    public function destroy($id): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $plan = Plan::find($id);
        if (!Gate::allows('destroy-plan', $plan)) return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        Plan::destroy($id);
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function upvote($id)
    {
        $plan = Plan::find($id);
        $votes = $plan->votos + 1;
        $plan->update(['votos' => $votes]);
    }

    public function downvote($id)
    {
        $plan = Plan::find($id);
        $votes = $plan->votos - 1;
        $plan->update(['votos' => $votes]);
    }

    public function route($id, $profile): mixed
    {
        $plan = Plan::find($id);
        if (!$plan) return response(['error' => 'No such plan'], Response::HTTP_BAD_REQUEST);
        if (!Gate::allows('read-plan', $plan)) return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);

        $planResource = new PlanResource($plan);
        $steps = json_decode($planResource->toJson())->pasos;
        if (sizeof($steps) < 2) return response(['error' => 'No enough plan steps'], Response::HTTP_BAD_REQUEST);

        $coordinatesString = '';
        foreach ($steps as $step) $coordinatesString .= $step->recurso->longitud . ',' . $step->recurso->latitud . ';';
        $coordinatesString = rtrim($coordinatesString, ';');

        $token = env('MAP_BOX_TOKEN');
        $response = Http::get("https://api.mapbox.com/directions/v5/mapbox/$profile/$coordinatesString?alternatives=false&geometries=geojson&language=es&overview=simplified&steps=false&access_token=$token");
        return $response->json();
    }

}
