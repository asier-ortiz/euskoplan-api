<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;


/**
 * App\Models\Cave
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
 * @property string|null $tipoMonumento
 * @property string|null $periodo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Image[] $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Plan[] $plans
 * @property-read int|null $plans_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Service[] $services
 * @property-read int|null $services_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Cave newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cave newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cave query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereCodigoMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereCodigoPostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereCodigoProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereFechaActualizacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereGmLatitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereGmLongitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereIdioma($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereNombreMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereNombreProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereNombreSubtipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereNumeroTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave wherePaginaWeb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave wherePeriodo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereSubtipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereTipoMonumento($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereTipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cave whereUrlFichaPortal($value)
 * @mixin \Eloquent
 */
class Cave extends Model
{
    use HasFactory;
    use HasSlug;

    protected $table = 'caves';

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
