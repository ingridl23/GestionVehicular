<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Date;

class Carnet extends Model
{
    use hasFactory, Notifiable;

    protected $fillable = [
        'fecha_vencimiento',
        'fecha_emision',
        'id_usuario',
        'vigente'
    ];
    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_emision' => 'date',
        'vigente' => 'boolean',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public static function carnetVigente($id){
         $carnet = Carnet::where('id_usuario', $id)->first();

        if (!$carnet || !$carnet->id_usuario) {
            return false;
        }
        
        $fecha_hoy = Date::now();
        return $fecha_hoy->lessThanOrEqualTo($carnet->fecha_vencimiento);
    }

}
