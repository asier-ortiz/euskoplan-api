<?php

namespace App\Models;

use App\Traits\Slug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;


/**
 * App\Models\Museum
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
 * @property string|null $codigoLocalidad
 * @property string|null $nombreProvincia
 * @property string|null $nombreMunicipio
 * @property string|null $nombreLocalidad
 * @property string|null $gmLongitud
 * @property string|null $gmLatitud
 * @property string|null $subTipoRecurso
 * @property string|null $nombreSubTipoRecurso
 * @property string|null $tematica
 * @property string|null $nombreTematica
 * @property string|null $capacidad
 * @property string|null $horario
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
 * @method static \Illuminate\Database\Eloquent\Builder|Museum newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Museum newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Museum query()
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereCapacidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereCodigoLocalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereCodigoMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereCodigoPostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereCodigoProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereFechaActualizacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereGmLatitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereGmLongitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereHorario($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereIdioma($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereNombreLocalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereNombreMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereNombreProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereNombreSubTipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereNombreTematica($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereNumeroTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum wherePaginaWeb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereSubTipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereTematica($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereTipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Museum whereUrlFichaPortal($value)
 * @mixin \Eloquent
 */
class Museum extends Model
{
    use HasFactory;
    use Slug;

    protected $table = 'museums';

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
