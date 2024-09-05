<?php

namespace App\Observers;

use App\Models\Plan;
use Illuminate\Support\Facades\Cache;

class PlanObserver
{
    /**
     * Handle the Plan "created" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function created(Plan $plan)
    {
        //
    }

    /**
     * Handle the Plan "updated" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function updated(Plan $plan)
    {
        $this->clearRouteCache($plan);
    }

    /**
     * Handle the Plan "deleted" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function deleted(Plan $plan)
    {
        $this->clearRouteCache($plan);
    }

    /**
     * Handle the Plan "restored" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function restored(Plan $plan)
    {
        //
    }

    /**
     * Handle the Plan "force deleted" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function forceDeleted(Plan $plan)
    {
        //
    }

    /**
     * Clear the cache for the plan's route.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    protected function clearRouteCache(Plan $plan): void
    {
        $profiles = ['walking', 'driving', 'cycling'];
        foreach ($profiles as $profile) {
            $steps = $plan->steps;
            $coordinatesString = implode(';', array_map(fn($step) => $step->recurso->longitud . ',' . $step->recurso->latitud, $steps->toArray()));
            $cacheKey = 'map_route_' . md5($plan->id . $profile . $coordinatesString);
            Cache::forget($cacheKey);
        }
    }
}
