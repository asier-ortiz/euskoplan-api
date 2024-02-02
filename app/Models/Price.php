<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;


/**
 * App\Models\Price
 *
 * @property int $id
 * @property string|null $codigo
 * @property string|null $nombre
 * @property int|null $capacidad
 * @property string|null $precioMinimo
 * @property string|null $precioMaximo
 * @property int $accommodation_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Accommodation $accommodation
 * @method static Builder|Price newModelQuery()
 * @method static Builder|Price newQuery()
 * @method static Builder|Price query()
 * @method static Builder|Price whereAccommodationId($value)
 * @method static Builder|Price whereCapacidad($value)
 * @method static Builder|Price whereCodigo($value)
 * @method static Builder|Price whereCreatedAt($value)
 * @method static Builder|Price whereId($value)
 * @method static Builder|Price whereNombre($value)
 * @method static Builder|Price wherePrecioMaximo($value)
 * @method static Builder|Price wherePrecioMinimo($value)
 * @method static Builder|Price whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Price extends Model
{
    use HasFactory;

    protected $table = 'prices';

    protected $guarded = [];

    protected $fillable = [];

    public $timestamps = true;

    protected $dates = ['updated_at', 'created_at'];

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

}
