<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function success($data = null, $message = 'Operación exitosa')
    {
        return [
            'success' => true,
            'cod_error' => '00',
            'message_error' => $message,
            'data' => $data,
        ];
    }

    public static function error($message, $cod_error = '400', $data = null)
    {
        return [
            'success' => false,
            'cod_error' => $cod_error,
            'message_error' => $message,
            'data' => $data,
        ];
    }
}
