<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarDependenciaRequest extends FormRequest
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
            'nombre'=>'required|string|min:2|max:200',
            'id_dependencia_padre'=>'required|integer|min:1|exists:dependencias,id',
            'id_direccion'=>'required|integer|min:1|exists:direcciones,id',
            'activa'=>'required',
        ];
    }

    public function messages()
    {
        return [
            //ver mensajes a devolver
        ];
    }
}
