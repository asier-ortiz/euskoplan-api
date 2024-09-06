<?php

namespace App\Models;

use App\Traits\HasSlug;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;


/**
 * App\Models\Restaurant
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
 * @property string|null $numeroTelefono2
 * @property string|null $email
 * @property string|null $paginaWeb
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
 * @property string|null $capacidad
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|\App\Models\Image[] $images
 * @property-read int|null $images_count
 * @property-read Collection|\App\Models\Plan[] $plans
 * @property-read int|null $plans_count
 * @property-read Collection|\App\Models\Service[] $services
 * @property-read int|null $services_count
 * @property-read Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Restaurant newModelQuery()
 * @method static Builder|Restaurant newQuery()
 * @method static Builder|Restaurant query()
 * @method static Builder|Restaurant whereCapacidad($value)
 * @method static Builder|Restaurant whereCodigo($value)
 * @method static Builder|Restaurant whereCodigoLocalidad($value)
 * @method static Builder|Restaurant whereCodigoMunicipio($value)
 * @method static Builder|Restaurant whereCodigoPostal($value)
 * @method static Builder|Restaurant whereCodigoProvincia($value)
 * @method static Builder|Restaurant whereCreatedAt($value)
 * @method static Builder|Restaurant whereDescripcion($value)
 * @method static Builder|Restaurant whereDireccion($value)
 * @method static Builder|Restaurant whereEmail($value)
 * @method static Builder|Restaurant whereFechaActualizacion($value)
 * @method static Builder|Restaurant whereGmLatitud($value)
 * @method static Builder|Restaurant whereGmLongitud($value)
 * @method static Builder|Restaurant whereId($value)
 * @method static Builder|Restaurant whereIdioma($value)
 * @method static Builder|Restaurant whereNombre($value)
 * @method static Builder|Restaurant whereNombreLocalidad($value)
 * @method static Builder|Restaurant whereNombreMunicipio($value)
 * @method static Builder|Restaurant whereNombreProvincia($value)
 * @method static Builder|Restaurant whereNombreSubtipoRecurso($value)
 * @method static Builder|Restaurant whereNumeroTelefono($value)
 * @method static Builder|Restaurant whereNumeroTelefono2($value)
 * @method static Builder|Restaurant wherePaginaWeb($value)
 * @method static Builder|Restaurant whereSubtipoRecurso($value)
 * @method static Builder|Restaurant whereTipoRecurso($value)
 * @method static Builder|Restaurant whereUpdatedAt($value)
 * @method static Builder|Restaurant whereUrlFichaPortal($value)
 * @mixin Eloquent
 */
class Restaurant extends Model
{
    use HasFactory;
    use HasSlug;

    protected $table = 'restaurants';

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

    public function plans(): MorphToMany
    {
        return $this->morphToMany(Plan::class, 'planables');
    }

    public function users(): MorphToMany
    {
        return $this->morphToMany(User::class, 'favouritables');
    }


}
