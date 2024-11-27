<?php

namespace App\Http\Requests\RoleRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del rol es requerido',
            'name.unique' => 'Este nombre de rol ya existe',
            'name.max' => 'El nombre no puede exceder los 255 caracteres',
            'description.max' => 'La descripción no puede exceder los 1000 caracteres'
        ];
    }
}