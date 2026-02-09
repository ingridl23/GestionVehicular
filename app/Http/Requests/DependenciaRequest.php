<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DependenciaRequest extends FormRequest
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
            'nombre'=>'required|string|min:2|max:100',
            'id_dependencia_padre'=>'nullable|integer|min:1|exists:dependencias,id',
            'id_direccion'=>'required',
            'activa'=>'required',
            'calle'  => 'required_if:id_direccion,nueva', 'string', 'max:255',
            'altura' => 'required_if:id_direccion,nueva', 'integer', 'min:1',
            'ciudad' => 'required_if:id_direccion,nueva', 'string', 'max:255',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre de la dependencia es obligatorio.',
            'nombre.min' => 'El nombre de la dependencia debe tener minimo dos letras.',
            'nombre.max' => 'El nombre de la dependencia puede tener como máximo 100 letras.',
            'activa.required' => 'Se debe marcar si la dependencia estará activa.',
            'id_direccion.required' => 'Debe seleccionar una dirección.',
            'altura.min' => 'La altura debe tener minino un dígito.',
            'calle.required_if'     => 'La calle es obligatoria.',
            'altura.required_if'    => 'La altura es obligatoria.',
            'ciudad.required_if'    => 'La ciudad es obligatoria.',
        ];
    }


}
