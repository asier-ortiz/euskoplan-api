<?php

namespace App\Models;

use App\Traits\Slug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;


/**
 * App\Models\Event
 *
 * @property int $id
 * @property string $fechaActualizacion
 * @property string $idioma
 * @property string|null $codigo
 * @property string|null $tipoRecurso
 * @property string|null $nombre
 * @property string|null $descripcion
 * @property string|null $urlFichaPortal
 * @property string|null $codigoProvincia
 * @property string|null $codigoMunicipio
 * @property string|null $codigoLocalidad
 * @property string|null $nombreProvincia
 * @property string|null $nombreMunicipio
 * @property string|null $nombreLocalidad
 * @property string|null $gmLongitud
 * @property string|null $gmLatitud
 * @property string|null $subtipoRecurso
 * @property string|null $nombreSubtipoRecurso
 * @property \Illuminate\Support\Carbon|null $fechaInicio
 * @property \Illuminate\Support\Carbon|null $fechaFin
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Image[] $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Plan[] $plans
 * @property-read int|null $plans_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCodigoLocalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCodigoMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCodigoProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereFechaActualizacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereFechaInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereGmLatitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereGmLongitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIdioma($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNombreLocalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNombreMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNombreProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNombreSubtipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereSubtipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereTipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUrlFichaPortal($value)
 * @mixin \Eloquent
 */
class Event extends Model
{
    use HasFactory;
    use Slug;

    protected $table = 'events';

    protected $guarded = [];

    protected $fillable = [];

    public $timestamps = true;

    protected $dates = ['updated_at', 'created_at', 'fechaInicio', 'fechaFin'];

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function plans(): MorphToMany
    {
        return $this->morphToMany(Plan::class, 'planables');
    }

    public function users(): MorphToMany
    {
        return $this->morphToMany(User::class, 'favouritables');
    }
}
