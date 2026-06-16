<?php

namespace App\Http\Requests;

use App\Rules\DespachoRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DespachoStoreRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "distribuidor_id" => "required",
            "observacion" => "nullable",
            "pedido_ids" => "required|array|min:1"
        ];
    }

    public function messages()
    {
        return [
            "distribuidor_id.required" => "Debes seleccionar un distribuidor",
            "pedido_ids.required" => "Debes seleccionar al menos un pedido",
            "pedido_ids.min" => "Debes seleccionar al menos un pedido",
        ];
    }
}
