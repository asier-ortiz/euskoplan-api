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
        $query = Plan::query()
            ->where('publico', true)
            ->when(request('idioma'), function (Builder $query) {
                $query->where('idioma', '=', request('idioma'));
            })
            ->when(request('titulo'), function (Builder $query) {
                $query->where('titulo', 'like', '%' . request('titulo') . '%');
            })
            ->when(request('descripcion'), function (Builder $query) {
                $query->where('descripcion', 'like', '%' . request('descripcion') . '%');
            })
            ->when(request('limite'), function (Builder $query) {
                $query->take(request('limite'));
            })
            ->orderBy('votos', 'desc');

        // Combinación de filtrado y búsqueda
        if (request('busqueda')) {
            $terms = explode(' ', request('busqueda'));
            $query->where(function (Builder $q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('titulo', 'like', '%' . $term . '%')
                        ->orWhere('descripcion', 'like', '%' . $term . '%')
                        ->orWhereHas('accommodations', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('caves', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('culturals', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('events', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('fairs', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('localities', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('museums', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('naturals', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('restaurants', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        });
                }
            });
        }

        return PlanResource::collection($query->get());
    }

    public function userPlans(): AnonymousResourceCollection
    {
        $user = request()->user();
        return PlanResource::collection(Plan::where('user_id', '=', $user->id)->get());
    }

    public function show($id): \Illuminate\Http\Response|PlanResource|Application|ResponseFactory
    {
        $plan = Plan::find($id);
        if (!$plan || !Gate::allows('read-plan', $plan)) {
            return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }
        return new PlanResource($plan);
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

        StepController::bulkStore($validatedData['pasos'], $plan->id);
        return response(new PlanResource($plan), Response::HTTP_CREATED);
    }

    public function update(PlanUpdateRequest $request, $id): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $plan = Plan::find($id);
        if (!Gate::allows('update-plan', $plan)) {
            return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }
        $validatedData = $request->validated();
        $plan->update($validatedData);
        return response(new PlanResource($plan), Response::HTTP_ACCEPTED);
    }

    public function destroy($id): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $plan = Plan::find($id);
        if (!Gate::allows('destroy-plan', $plan)) {
            return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }
        $plan->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function upvote($id): void
    {
        $plan = Plan::find($id);
        $plan->increment('votos');
    }

    public function downvote($id): void
    {
        $plan = Plan::find($id);
        $plan->decrement('votos');
    }

    public function route($id, $profile): mixed
    {
        $plan = Plan::find($id);
        if (!$plan || !Gate::allows('read-plan', $plan)) {
            return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $planResource = new PlanResource($plan);
        $steps = json_decode($planResource->toJson())->pasos;
        if (count($steps) < 2) {
            return response(['error' => 'Not enough plan steps'], Response::HTTP_BAD_REQUEST);
        }

        $coordinatesString = implode(';', array_map(fn($step) => $step->recurso->longitud . ',' . $step->recurso->latitud, $steps));

        $token = env('MAP_BOX_TOKEN');
        $response = Http::get("https://api.mapbox.com/directions/v5/mapbox/$profile/$coordinatesString?alternatives=false&geometries=geojson&language=es&overview=simplified&steps=false&access_token=$token");
        return $response->json();
    }
}
