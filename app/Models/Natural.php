<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;


/**
 * App\Models\Natural
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
 * @property string|null $subTipoRecursoEspacioNatural
 * @property string|null $nombreSubTipoRecursoEspacioNatural
 * @property string|null $fauna
 * @property string|null $flora
 * @property string|null $subTipoRecursoPlayasPantanosRios
 * @property string|null $nombreSubTipoRecursoPlayasPantanosRios
 * @property string|null $horario
 * @property string|null $actividades
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
 * @method static \Illuminate\Database\Eloquent\Builder|Natural newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Natural newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Natural query()
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereActividades($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereCodigoMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereCodigoPostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereCodigoProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereFauna($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereFechaActualizacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereFlora($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereGmLatitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereGmLongitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereHorario($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereIdioma($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereNombreMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereNombreProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereNombreSubTipoRecursoEspacioNatural($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereNombreSubTipoRecursoPlayasPantanosRios($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereNumeroTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural wherePaginaWeb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereSubTipoRecursoEspacioNatural($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereSubTipoRecursoPlayasPantanosRios($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereTipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Natural whereUrlFichaPortal($value)
 * @mixin \Eloquent
 */
class Natural extends Model
{
    use HasFactory;

    protected $table = 'naturals';

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
