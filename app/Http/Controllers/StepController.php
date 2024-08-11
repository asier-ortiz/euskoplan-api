<?php

namespace App\Http\Controllers;

use App\Http\Requests\StepCreateRequest;
use App\Http\Requests\StepUpdateRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Models\Step;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class StepController extends Controller
{
    public function store(StepCreateRequest $request, $planId): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $plan = Plan::find($planId);
        if (!$plan || !Gate::allows('update-plan', $plan)) {
            return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $validatedData = $request->validated();
        Step::create([
            'indice' => $validatedData['indice'],
            'indicaciones' => $validatedData['indicaciones'],
            'plan_id' => $plan->id,
            'planables_id' => $validatedData['id_recurso'],
            'planables_type' => $validatedData['tipo_recurso']
        ]);

        return response(new PlanResource($plan), Response::HTTP_ACCEPTED);
    }

    public static function bulkStore($steps, $planId): void
    {
        $stepData = array_map(fn($step) => [
            'indice' => $step['indice'],
            'indicaciones' => $step['indicaciones'],
            'plan_id' => $planId,
            'planables_id' => $step['id_recurso'],
            'planables_type' => $step['tipo_recurso']
        ], $steps);
        Step::insert($stepData);
    }

    public function update(StepUpdateRequest $request, $id): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $step = Step::find($id);
        $plan = Plan::find($step->plan_id);
        if (!$plan || !Gate::allows('update-plan', $plan)) {
            return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $validatedData = $request->validated();
        $step->update($validatedData);

        return response(new PlanResource($plan), Response::HTTP_ACCEPTED);
    }

    public function destroy($id): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $step = Step::find($id);
        $plan = Plan::find($step->plan_id);
        if (!$plan || !Gate::allows('destroy-plan', $plan)) {
            return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }
        $step->delete();
        return response(new PlanResource($plan), Response::HTTP_ACCEPTED);
    }
}
