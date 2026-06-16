<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            "usuario" => "required|min:2|unique:users,usuario," . $this->user->id,
            "nombre" => "required|min:2",
            "acceso" => "required",
            "bloqueo" => "required",
            "password" => "nullable|min:6",
            "tipo" => "required",
            "foto" => "nullable",
        ];
    }

    /**
     * Mensages validacion
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            "usuario.required" => "Este campo es obligatorio",
            "usuario.min" => "Debes ingresar al menos :min caracteres",
            "usuario.unique" => "Este usuario no esta disponible",
            "nombre.required" => "Este campo es obligatorio",
            "nombre.min" => "Debes ingresar al menos :min caracteres",
            "paterno.required" => "Este campo es obligatorio",
            "paterno.min" => "Debes ingresar al menos :min caracteres",
            "ci.required" => "Este campo es obligatorio",
            "ci.unique" => "Este C.I. ya fue registrado",
            "ci_exp.required" => "Este campo es obligatorio",
            "fono.required" => "Este campo es obligatorio",
            "fono.min" => "Debes ingresar al menos :min caracteres",
            "acceso.required" => "Este campo es obligatorio",
            "bloqueo.required" => "Este campo es obligatorio",
            "tipo.required" => "Este campo es obligatorio",
        ];
    }
}
