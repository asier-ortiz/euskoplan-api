<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;


/**
 * App\Models\User
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|\App\Models\Accommodation[] $favouriteAccommodations
 * @property-read int|null $favourite_accommodations_count
 * @property-read Collection|\App\Models\Cave[] $favouriteCaves
 * @property-read int|null $favourite_caves_count
 * @property-read Collection|\App\Models\Cultural[] $favouriteCulturals
 * @property-read int|null $favourite_culturals_count
 * @property-read Collection|\App\Models\Event[] $favouriteEvents
 * @property-read int|null $favourite_events_count
 * @property-read Collection|\App\Models\Fair[] $favouriteFairs
 * @property-read int|null $favourite_fairs_count
 * @property-read Collection|\App\Models\Locality[] $favouriteLocalities
 * @property-read int|null $favourite_localities_count
 * @property-read Collection|\App\Models\Museum[] $favouriteMuseums
 * @property-read int|null $favourite_museums_count
 * @property-read Collection|\App\Models\Natural[] $favouriteNaturals
 * @property-read int|null $favourite_naturals_count
 * @property-read Collection|\App\Models\Plan[] $favouritePlans
 * @property-read int|null $favourite_plans_count
 * @property-read Collection|\App\Models\Restaurant[] $favouriteRestaurants
 * @property-read int|null $favourite_restaurants_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|\App\Models\Plan[] $plans
 * @property-read int|null $plans_count
 * @property-read Collection|PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password'
    ];

    protected $guarded = [];

    protected $hidden = ['password'];

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function favouritePlans(): BelongsToMany
    {
        return $this->morphedByMany(Plan::class, 'favouritables')
            ->as('favourite')
            ->withPivot('id');
    }

    public function favouriteAccommodations(): BelongsToMany
    {
        return $this->morphedByMany(Accommodation::class, 'favouritables')
            ->withPivot('id');
    }

    public function favouriteCaves(): BelongsToMany
    {
        return $this->morphedByMany(Cave::class, 'favouritables')
            ->withPivot('id');
    }

    public function favouriteCulturals(): BelongsToMany
    {
        return $this->morphedByMany(Cultural::class, 'favouritables')
            ->withPivot('id');
    }

    public function favouriteEvents(): BelongsToMany
    {
        return $this->morphedByMany(Event::class, 'favouritables')
            ->withPivot('id');
    }

    public function favouriteFairs(): BelongsToMany
    {
        return $this->morphedByMany(Fair::class, 'favouritables')
            ->withPivot('id');
    }

    public function favouriteLocalities(): BelongsToMany
    {
        return $this->morphedByMany(Locality::class, 'favouritables')
            ->withPivot('id');
    }

    public function favouriteMuseums(): BelongsToMany
    {
        return $this->morphedByMany(Museum::class, 'favouritables')
            ->withPivot('id');
    }

    public function favouriteNaturals(): BelongsToMany
    {
        return $this->morphedByMany(Natural::class, 'favouritables')
            ->withPivot('id');
    }

    public function favouriteRestaurants(): BelongsToMany
    {
        return $this->morphedByMany(Restaurant::class, 'favouritables')
            ->withPivot('id');
    }

}
