<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;


/**
 * App\Models\Locality
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
 * @property string|null $numHabitantes
 * @property string|null $superficie
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Image[] $images
 * @property-read int|null $images_count
 * @method static \Illuminate\Database\Eloquent\Builder|Locality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Locality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Locality query()
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereCodigoLocalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereCodigoMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereCodigoProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereFechaActualizacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereGmLatitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereGmLongitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereIdioma($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereNombreLocalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereNombreMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereNombreProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereNumHabitantes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereSuperficie($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereTipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereUrlFichaPortal($value)
 * @mixin \Eloquent
 */
class Locality extends Model
{
    use HasFactory;

    protected $table = 'localities';

    protected $guarded = [];

    protected $fillable = [];

    public $timestamps = true;

    protected $dates = ['updated_at', 'created_at'];

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
