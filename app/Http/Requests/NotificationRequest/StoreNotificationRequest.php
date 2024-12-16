<?php

namespace App\Http\Requests\NotificationRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validación fallida',
            'error' => $validator->errors(),
        ], 422));
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'importance' => ['required', 'string', 'in:low,moderate,high'],
            'notification_type' => ['required', 'string', 'in:info,warning,alert'],
            'content' => ['required', 'string'],
            'related_url' => ['nullable', 'string', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El usuario es obligatorio.',
            'user_id.exists' => 'El usuario seleccionado no existe.',
            'importance.required' => 'La importancia es obligatoria.',
            'importance.string' => 'La importancia debe ser texto.',
            'importance.in' => 'La importancia debe ser low, moderate o high.',
            'notification_type.required' => 'El tipo de notificación es obligatorio.',
            'notification_type.string' => 'El tipo de notificación debe ser texto.',
            'notification_type.in' => 'El tipo de notificación debe ser info, warning o alert.',
            'content.required' => 'El contenido es obligatorio.',
            'content.string' => 'El contenido debe ser texto.',
            'related_url.url' => 'La URL relacionada debe ser válida.',
            'related_url.string' => 'La URL relacionada debe ser texto.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_read' => false,
        ]);
    }
}
