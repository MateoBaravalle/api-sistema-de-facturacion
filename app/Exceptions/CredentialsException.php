<?php

namespace App\Exceptions;

use Exception;

class CredentialsException extends Exception
{
    public function __construct(string $message = 'Credenciales incorrectas', int $code = 401)
    {
        parent::__construct($message, $code);
    }

    public function render()
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Autenticación fallida',
            'error' => $this->getMessage(),
        ], $this->getCode());
    }
}
