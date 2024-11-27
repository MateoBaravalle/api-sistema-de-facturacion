<?php

namespace App\Http\Requests\RoleRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255|unique:roles,name,' . $this->route('id'),
            'description' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Este nombre de rol ya existe',
            'name.max' => 'El nombre no puede exceder los 255 caracteres',
            'description.max' => 'La descripción no puede exceder los 1000 caracteres'
        ];
    }
}