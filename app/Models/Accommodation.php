<?php

namespace App\Models;

use App\Traits\HasSlug;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;


/**
 * App\Models\Accommodation
 *
 * @property int $id
 * @property string $fechaActualizacion
 * @property string $idioma
 * @property string|null $codigo
 * @property string|null $tipoRecurso
 * @property string|null $nombre
 * @property string|null $descripcion
 * @property string|null $urlFichaPortal
 * @property string|null $direccion
 * @property string|null $codigoPostal
 * @property string|null $numeroTelefono
 * @property string|null $email
 * @property string|null $paginaWeb
 * @property string|null $codigoProvincia
 * @property string|null $codigoMunicipio
 * @property string|null $nombreProvincia
 * @property string|null $nombreMunicipio
 * @property string|null $gmLongitud
 * @property string|null $gmLatitud
 * @property string|null $subtipoRecurso
 * @property string|null $nombreSubtipoRecurso
 * @property string|null $categoria
 * @property string|null $capacidad
 * @property string|null $annoApertura
 * @property string|null $numHabIndividuales
 * @property string|null $numHabDobles
 * @property string|null $numHabSalon
 * @property string|null $numHabHasta4Plazas
 * @property string|null $numHabMas4Plazas
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|\App\Models\Image[] $images
 * @property-read int|null $images_count
 * @property-read Collection|\App\Models\Plan[] $plans
 * @property-read int|null $plans_count
 * @property-read Collection|\App\Models\Price[] $prices
 * @property-read int|null $prices_count
 * @property-read Collection|\App\Models\Service[] $services
 * @property-read int|null $services_count
 * @property-read Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Accommodation newModelQuery()
 * @method static Builder|Accommodation newQuery()
 * @method static Builder|Accommodation query()
 * @method static Builder|Accommodation whereAnnoApertura($value)
 * @method static Builder|Accommodation whereCapacidad($value)
 * @method static Builder|Accommodation whereCategoria($value)
 * @method static Builder|Accommodation whereCodigo($value)
 * @method static Builder|Accommodation whereCodigoMunicipio($value)
 * @method static Builder|Accommodation whereCodigoPostal($value)
 * @method static Builder|Accommodation whereCodigoProvincia($value)
 * @method static Builder|Accommodation whereCreatedAt($value)
 * @method static Builder|Accommodation whereDescripcion($value)
 * @method static Builder|Accommodation whereDireccion($value)
 * @method static Builder|Accommodation whereEmail($value)
 * @method static Builder|Accommodation whereFechaActualizacion($value)
 * @method static Builder|Accommodation whereGmLatitud($value)
 * @method static Builder|Accommodation whereGmLongitud($value)
 * @method static Builder|Accommodation whereId($value)
 * @method static Builder|Accommodation whereIdioma($value)
 * @method static Builder|Accommodation whereNombre($value)
 * @method static Builder|Accommodation whereNombreMunicipio($value)
 * @method static Builder|Accommodation whereNombreProvincia($value)
 * @method static Builder|Accommodation whereNombreSubtipoRecurso($value)
 * @method static Builder|Accommodation whereNumHabDobles($value)
 * @method static Builder|Accommodation whereNumHabHasta4Plazas($value)
 * @method static Builder|Accommodation whereNumHabIndividuales($value)
 * @method static Builder|Accommodation whereNumHabMas4Plazas($value)
 * @method static Builder|Accommodation whereNumHabSalon($value)
 * @method static Builder|Accommodation whereNumeroTelefono($value)
 * @method static Builder|Accommodation wherePaginaWeb($value)
 * @method static Builder|Accommodation whereSubtipoRecurso($value)
 * @method static Builder|Accommodation whereTipoRecurso($value)
 * @method static Builder|Accommodation whereUpdatedAt($value)
 * @method static Builder|Accommodation whereUrlFichaPortal($value)
 * @mixin Eloquent
 */
class Accommodation extends Model
{
    use HasFactory;
    use HasSlug;

    protected $table = 'accommodations';

    protected $guarded = [];

    protected $fillable = [];

    public $timestamps = true;

    protected $dates = ['updated_at', 'created_at'];

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function services(): MorphToMany
    {
        return $this->morphToMany(Service::class, 'serviceables');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
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
