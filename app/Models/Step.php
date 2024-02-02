<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Step
 *
 * @property int $id
 * @property int $indice
 * @property string|null $indicaciones
 * @property int $plan_id
 * @property int $planables_id
 * @property string $planables_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Step newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Step newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Step query()
 * @method static \Illuminate\Database\Eloquent\Builder|Step whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Step whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Step whereIndicaciones($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Step whereIndice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Step wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Step wherePlanablesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Step wherePlanablesType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Step whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Step extends Model
{
    use HasFactory;

    protected $table = 'planables';

    public $timestamps = true;

    protected $guarded = [];

    protected $fillable = [];

    protected $dates = ['updated_at', 'created_at'];

}
