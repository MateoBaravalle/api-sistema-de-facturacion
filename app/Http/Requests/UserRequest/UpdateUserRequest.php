<?php

namespace App\Http\Requests\UserRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'error' => $validator->errors(),
        ], 422));
    }

    public function rules(): array
    {
        $userId = $this->route('user');
        
        return [
            'username' => ['sometimes', 'string', 'max:255', 'unique:users,username,' . $userId, 'regex:/^[a-zA-Z0-9._-]+$/'],
            'name' => ['sometimes', 'string', 'max:255'],
            'lastname' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $userId],
            'password' => ['sometimes', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'role' => ['sometimes', 'exists:roles,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.string' => 'El nombre de usuario debe ser una cadena de texto',
            'username.max' => 'El nombre de usuario no debe exceder 255 caracteres',
            'username.unique' => 'Este nombre de usuario ya está en uso',
            'username.regex' => 'El nombre de usuario solo puede contener letras, números y los caracteres . _ -',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre no debe exceder 255 caracteres',
            'lastname.string' => 'El apellido debe ser una cadena de texto',
            'lastname.max' => 'El apellido no debe exceder 255 caracteres',
            'email.email' => 'Debe ser un correo electrónico válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'password.string' => 'La contraseña debe ser una cadena de texto',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'phone.max' => 'El teléfono no debe exceder 20 caracteres',
            'phone.regex' => 'El teléfono solo puede contener números',
        ];
    }
}