<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FiltroDependenciasRequest extends FormRequest
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
            'nombre'=>'nullable|string|min:1|max:200',
            'id_dependencia_padre'=>'nullable|integer|min:1|exists:dependencia,id',
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
