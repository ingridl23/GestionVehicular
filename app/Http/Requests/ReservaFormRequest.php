<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservaFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    // VER, DETALLAR
    public function rules(): array
    {
        return [
            'fecha_inicio'=>'required|date|after:now',
            'fecha_fin'=>'required|date|after_or_equal:fecha_inicio',
            'id_vehiculo'=>'required|integer|exists:vehiculos,id|not_in:default',
            'id_usuario'=>'required|integer|exists:users,id|not_in:default',
            'id_dependencia'=>'required|integer|exists:dependencias,id',
        ];
    }

    public function messages(){
        return [
            'fecha_inicio.required' => 'Debe indicar la fecha y el horario de inicio de la reserva.',
            'fecha_inicio.date'     => 'La fecha de inicio ingresada no es válida.',
            'fecha_inicio.after'    => 'La fecha y el horario de inicio deben ser posteriores al momento actual.',

            'fecha_fin.required'       => 'Debe indicar la fecha y el horario de finalización de la reserva.',
            'fecha_fin.date'           => 'La fecha de finalización ingresada no es válida.',
            'fecha_fin.after_or_equal' => 'La fecha y el horario de finalización deben ser posteriores o iguales a la fecha de inicio.',

            'id_vehiculo.required' => 'Debe seleccionar un vehículo.',
            'id_vehiculo.integer'  => 'El vehículo seleccionado no es válido.',
            'id_vehiculo.exists'   => 'El vehículo seleccionado no existe.',

            'id_usuario.required' => 'Debe seleccionar un usuario responsable.',
            'id_usuario.integer'  => 'El usuario seleccionado no es válido.',
            'id_usuario.exists'   => 'El usuario seleccionado no existe.',
            
            'id_dependencia.required' => 'Debe seleccionar una dependencia para guardar la reserva.',
            'id_dependencia.integer'  => 'La dependencia seleccionada no es válida.',
            'id_dependencia.exists'   => 'La dependencia seleccionada no existe.',
        ];
    }
}
