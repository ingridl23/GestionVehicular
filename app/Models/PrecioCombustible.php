<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrecioCombustible extends Model
{
protected $table = 'precios_combustible';

protected $fillable = [
'precio_litro',
'fecha'
];

}
