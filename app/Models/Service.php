<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;


/**
 * App\Models\Service
 *
 * @property int $id
 * @property string $codigo
 * @property string $nombre
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|\App\Models\Accommodation[] $accommodations
 * @property-read int|null $accommodations_count
 * @property-read Collection|\App\Models\Cave[] $caves
 * @property-read int|null $caves_count
 * @property-read Collection|\App\Models\Cultural[] $culturals
 * @property-read int|null $culturals_count
 * @property-read Collection|\App\Models\Museum[] $museums
 * @property-read int|null $museums_count
 * @property-read Collection|\App\Models\Natural[] $naturals
 * @property-read int|null $naturals_count
 * @property-read Collection|\App\Models\Restaurant[] $restaurants
 * @property-read int|null $restaurants_count
 * @method static Builder|Service newModelQuery()
 * @method static Builder|Service newQuery()
 * @method static Builder|Service query()
 * @method static Builder|Service whereCodigo($value)
 * @method static Builder|Service whereCreatedAt($value)
 * @method static Builder|Service whereId($value)
 * @method static Builder|Service whereNombre($value)
 * @method static Builder|Service whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $guarded = [];

    protected $fillable = [];

    public $timestamps = true;

    protected $dates = ['updated_at', 'created_at'];


    public function accommodations(): BelongsToMany
    {
        return $this->morphedByMany(Accommodation::class, 'serviceables');
    }

    public function caves(): BelongsToMany
    {
        return $this->morphedByMany(Cave::class, 'serviceables');
    }

    public function culturals(): BelongsToMany
    {
        return $this->morphedByMany(Cultural::class, 'serviceables');
    }

    public function museums(): BelongsToMany
    {
        return $this->morphedByMany(Museum::class, 'serviceables');
    }

    public function naturals(): BelongsToMany
    {
        return $this->morphedByMany(Natural::class, 'serviceables');
    }

    public function restaurants(): BelongsToMany
    {
        return $this->morphedByMany(Restaurant::class, 'serviceables');
    }

}
