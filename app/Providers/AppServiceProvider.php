<?php

namespace App\Providers;

use App\Models\EmailVerify;
use App\Models\Favourite;
use App\Models\PasswordReset;
use App\Models\Plan;
use App\Models\Step;
use App\Models\User;
use App\Observers\EmailVerifyObserver;
use App\Observers\PasswordResetObserver;
use App\Observers\PlanObserver;
use App\Observers\StepObserver;
use App\Observers\UserObserver;
use Gate;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {

        // Ref: https://laravel.com/docs/9.x/eloquent-relationships#custom-polymorphic-types

        Relation::enforceMorphMap([
            'accommodation' => 'App\Models\Accommodation',
            'cave' => 'App\Models\Cave',
            'cultural' => 'App\Models\Cultural',
            'event' => 'App\Models\Event',
            'fair' => 'App\Models\Fair',
            'image' => 'App\Models\Image',
            'locality' => 'App\Models\Locality',
            'museum' => 'App\Models\Museum',
            'natural' => 'App\Models\Natural',
            'plan' => 'App\Models\Plan',
            'price' => 'App\Models\Price',
            'restaurant' => 'App\Models\Restaurant',
            'service' => 'App\Models\Service',
            'user' => 'App\Models\User',
        ]);

        JsonResource::withoutWrapping();

        // Ref:  https://laravel.com/docs/9.x/authorization

        Gate::define('read-plan', function (User $user, Plan $plan) {
            return $plan->publico == true || $user->id === $plan->user_id;
        });

        Gate::define('update-plan', function (User $user, Plan $plan) {
            return $user->id === $plan->user_id;
        });

        Gate::define('destroy-plan', function (User $user, Plan $plan) {
            return $user->id === $plan->user_id;
        });

        Gate::define('destroy-favourite', function (User $user, Favourite $favourite) {
            return $user->id === $favourite->user_id;
        });

        // Observers
        User::observe(UserObserver::class);
        EmailVerify::observe(EmailVerifyObserver::class);
        PasswordReset::observe(PasswordResetObserver::class);
        Plan::observe(PlanObserver::class);
        Step::observe(StepObserver::class);
    }
}
