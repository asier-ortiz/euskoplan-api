<?php

namespace App\Observers;

use App\Models\Plan;
use App\Models\Step;
use Illuminate\Support\Facades\Cache;

class StepObserver
{
    /**
     * Handle the Step "created" event.
     *
     * @param  \App\Models\Step  $step
     * @return void
     */
    public function created(Step $step)
    {
        //
    }

    /**
     * Handle the Step "updated" event.
     *
     * @param  \App\Models\Step  $step
     * @return void
     */
    public function updated(Step $step)
    {
        $this->clearRouteCache($step->plan_id);
    }

    /**
     * Handle the Step "deleted" event.
     *
     * @param  \App\Models\Step  $step
     * @return void
     */
    public function deleted(Step $step)
    {
        $this->clearRouteCache($step->plan_id);
    }

    /**
     * Handle the Step "restored" event.
     *
     * @param  \App\Models\Step  $step
     * @return void
     */
    public function restored(Step $step)
    {
        //
    }

    /**
     * Handle the Step "force deleted" event.
     *
     * @param  \App\Models\Step  $step
     * @return void
     */
    public function forceDeleted(Step $step)
    {
        //
    }

    /**
     * Clear the cached route for the plan when the steps are updated or deleted.
     *
     * @param  int  $planId
     * @return void
     */
    protected function clearRouteCache($planId)
    {
        $plan = Plan::find($planId);
        if ($plan) {
            $profiles = ['walking', 'driving', 'cycling'];
            foreach ($profiles as $profile) {
                $steps = $plan->steps;
                $coordinatesString = implode(';', array_map(fn($step) => $step->recurso->longitud . ',' . $step->recurso->latitud, $steps->toArray()));
                $cacheKey = 'map_route_' . md5($plan->id . $profile . $coordinatesString);
                Cache::forget($cacheKey);
            }
        }
    }
}
