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
        if (!Gate::allows('update-plan', $plan)) abort(Response::HTTP_FORBIDDEN);

        $validatedData = $request->validated();

        $step = Step::create([
            'indice' => $validatedData['indice'],
            'indicaciones' => $validatedData['indicaciones'],
            'plan_id' => $plan->id,
            'planables_id' => $validatedData['id_recurso'],
            'planables_type' => $validatedData['tipo_recurso']
        ]);

        $plan = Plan::find($step->plan_id);
        return response(new PlanResource($plan), Response::HTTP_ACCEPTED);
    }

    public static function bulkStore($steps)
    {
        Step::insert($steps);
    }

    public function update(StepUpdateRequest $request, $id): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $step = Step::find($id);
        $plan = Plan::find($step->plan_id);
        if (!Gate::allows('update-plan', $plan)) abort(Response::HTTP_FORBIDDEN);

        $validatedData = $request->validated();

        $step->update([
            'indice' => $validatedData['indice'],
            'indicaciones' => $validatedData['indicaciones'],
            'planables_id' => $validatedData['id_recurso'],
            'planables_type' => $validatedData['tipo_recurso']
        ]);

        $plan = Plan::find($step->plan_id);
        return response(new PlanResource($plan), Response::HTTP_ACCEPTED);
    }

    public function destroy($id): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $step = Step::find($id);
        $plan = Plan::find($step->plan_id);
        if (!Gate::allows('destroy-plan', $plan)) abort(Response::HTTP_FORBIDDEN);
        Step::destroy($id);
        $plan = Plan::find($step->plan_id);
        return response(new PlanResource($plan), Response::HTTP_ACCEPTED);
    }

}
