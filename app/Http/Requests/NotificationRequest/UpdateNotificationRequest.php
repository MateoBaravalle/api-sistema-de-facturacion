<?php

namespace App\Http\Requests\NotificationRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateNotificationRequest extends FormRequest
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
            'importance' => ['sometimes', 'string', 'in:low,moderate,high'],
            'notification_type' => ['sometimes', 'string', 'in:info,warning,alert'],
            'content' => ['sometimes', 'string'],
            'is_read' => ['sometimes', 'boolean'],
            'related_url' => ['nullable', 'string', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'importance.string' => 'La importancia debe ser texto.',
            'importance.in' => 'La importancia debe ser low, moderate o high.',
            'notification_type.string' => 'El tipo de notificación debe ser texto.',
            'notification_type.in' => 'El tipo de notificación debe ser info, warning o alert.',
            'content.string' => 'El contenido debe ser texto.',
            'is_read.boolean' => 'El campo leído debe ser verdadero o falso.',
            'related_url.url' => 'La URL relacionada debe ser válida.',
            'related_url.string' => 'La URL relacionada debe ser texto.',
        ];
    }
}
