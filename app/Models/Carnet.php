<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Carnet extends Model
{
    use hasFactory, Notifiable;

    protected $fillable = [
        'fecha_vencimiento',
        'fecha_emision',
        'id_usuario'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'id_usuario');
    }

}
