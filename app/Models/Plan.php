<?php

namespace App\Models;

use App\Traits\Slug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Models\Plan
 *
 * @property int $id
 * @property string $idioma
 * @property string $titulo
 * @property string|null $descripcion
 * @property int $votos
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $publico
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Accommodation[] $accommodations
 * @property-read int|null $accommodations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Cave[] $caves
 * @property-read int|null $caves_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Cultural[] $culturals
 * @property-read int|null $culturals_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Fair[] $fairs
 * @property-read int|null $fairs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Locality[] $localities
 * @property-read int|null $localities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Museum[] $museums
 * @property-read int|null $museums_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Natural[] $naturals
 * @property-read int|null $naturals_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Restaurant[] $restaurants
 * @property-read int|null $restaurants_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereIdioma($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereTitulo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereVotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan wherePublico($value)
 * @method static \Database\Factories\PlanFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class Plan extends Model
{
    use HasFactory;
    use Slug;

    protected $table = 'plans';

    public $timestamps = true;

    protected $fillable = [
        'idioma', 'titulo', 'descripcion', 'votos', 'publico', 'user_id'
    ];

    protected $guarded = ['id'];

    protected $dates = ['created_at', 'updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accommodations(): MorphToMany
    {
        return $this->morphedByMany(Accommodation::class, 'planables')
            ->withPivot('id', 'indice', 'indicaciones');
    }

    public function caves(): MorphToMany
    {
        return $this->morphedByMany(Cave::class, 'planables')
            ->withPivot('id', 'indice', 'indicaciones');
    }

    public function culturals(): MorphToMany
    {
        return $this->morphedByMany(Cultural::class, 'planables')
            ->withPivot('id', 'indice', 'indicaciones');
    }

    public function events(): MorphToMany
    {
        return $this->morphedByMany(Event::class, 'planables')
            ->withPivot('id', 'indice', 'indicaciones');
    }

    public function fairs(): MorphToMany
    {
        return $this->morphedByMany(Fair::class, 'planables')
            ->withPivot('id', 'indice', 'indicaciones');
    }

    public function localities(): MorphToMany
    {
        return $this->morphedByMany(Locality::class, 'planables')
            ->withPivot('id', 'indice', 'indicaciones');
    }

    public function museums(): MorphToMany
    {
        return $this->morphedByMany(Museum::class, 'planables')
            ->withPivot('id', 'indice', 'indicaciones');
    }

    public function naturals(): MorphToMany
    {
        return $this->morphedByMany(Natural::class, 'planables')
            ->withPivot('id', 'indice', 'indicaciones');
    }

    public function restaurants(): MorphToMany
    {
        return $this->morphedByMany(Restaurant::class, 'planables')
            ->withPivot('id', 'indice', 'indicaciones');
    }

    public function users(): MorphToMany
    {
        return $this->morphToMany(User::class, 'favouritables');
    }

    protected function slugSource(): string
    {
        return 'titulo';
    }
}
