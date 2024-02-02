<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;


/**
 * App\Models\Fair
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
 * @property string|null $atracciones
 * @property string|null $horario
 * @property string|null $tarifas
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Image[] $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Plan[] $plans
 * @property-read int|null $plans_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Fair newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fair newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fair query()
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereAtracciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereCodigoMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereCodigoPostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereCodigoProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereFechaActualizacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereGmLatitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereGmLongitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereHorario($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereIdioma($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereNombreMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereNombreProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereNumeroTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair wherePaginaWeb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereTarifas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereTipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fair whereUrlFichaPortal($value)
 * @mixin \Eloquent
 */
class Fair extends Model
{
    use HasFactory;

    protected $table = 'fairs';

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
