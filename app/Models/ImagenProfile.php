<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

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
     */
public function user()
{
    return $this->hasOne(User::class, 'id_photo_profile');
}


}
