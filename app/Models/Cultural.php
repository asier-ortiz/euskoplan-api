<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;


/**
 * App\Models\Cultural
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
 * @property string|null $subtipoRecurso
 * @property string|null $nombreSubtipoRecurso
 * @property string|null $tipoMonumento
 * @property string|null $estiloArtistico
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
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereCodigoLocalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereCodigoMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereCodigoPostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereCodigoProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereEstiloArtistico($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereFechaActualizacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereGmLatitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereGmLongitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereIdioma($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereNombreLocalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereNombreMunicipio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereNombreProvincia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereNombreSubtipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereNumeroTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural wherePaginaWeb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereSubtipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereTipoMonumento($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereTipoRecurso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cultural whereUrlFichaPortal($value)
 * @mixin \Eloquent
 */
class Cultural extends Model
{
    use HasFactory;
    use HasSlug;

    protected $table = 'culturals';

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
