<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailVerify
 *
 * @property int $id
 * @property string $email
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerify newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerify newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerify query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerify whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerify whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerify whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerify whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerify whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EmailVerify extends Model
{
    protected $table = 'user_verification_tokens';

    protected $fillable = ['email', 'token'];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

}
