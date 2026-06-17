<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CompraStoreRequest extends FormRequest
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
            "productos" => "required|array|min:1",
            "productos.*.categoria_producto_id" => "required",
            "productos.*.producto_id" => "required",
            "productos.*.cantidad" => "required|numeric|min:1",
            "productos.*.precio_compra" => "required|numeric|min:0",
            "productos.*.total" => "required|numeric|min:0",
        ];
    }

    public function messages()
    {
        return [
            "productos.required" => "Debe agregar al menos un producto al carrito",
            "productos.min" => "El carrito no puede estar vacío",
            "productos.*.categoria_producto_id.required" => "Falta la categoría de un producto",
            "productos.*.producto_id.required" => "Falta el ID del producto",
            "productos.*.cantidad.required" => "Falta ingresar la cantidad en un producto",
            "productos.*.cantidad.numeric" => "La cantidad debe ser numérica",
            "productos.*.precio_compra.required" => "Falta el precio de compra en un producto",
            "productos.*.precio_compra.numeric" => "El precio debe ser numérico",
            "productos.*.total.required" => "El total del producto no puede estar vacío",
        ];
    }
}
