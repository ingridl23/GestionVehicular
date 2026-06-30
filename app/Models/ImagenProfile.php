<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @class ImagenProfile
 *
 * Representa un carnet  de conducir dentro del sistema en relacion a un usuario existente.
 *
 * @package App\Models
 * @property int $id Identificador único
 * @property string $public_id Nombre de la categoria
 * @property string $url_photo_profile Nombre de la categoria
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 *
 */
class ImagenProfile extends Model
{
    use HasFactory;
    protected $table = 'image_profile_user';

    protected $fillable = [
        'public_id',
        'url_photo_profile',
        'created_at',
        'updated_at'

    ];


    /**
     * Relacion: imagen con usuarios
     *
     */
public function user()
{
    return $this->hasOne(User::class, 'id_photo_profile');
}


}
