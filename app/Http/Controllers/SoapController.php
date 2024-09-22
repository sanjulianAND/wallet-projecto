<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

class SoapController extends Controller
{
    public function handle()
    {
        $options = [
            'uri' => 'http://127.0.0.1:8000/soap-server',
            'location' => 'http://127.0.0.1:8000/soap-server'
        ];

        $server = new \SoapServer(null, $options);
        $server->setObject($this);
        $server->handle();
    }

    public function registerClient($documento, $nombres, $email, $celular)
    {
        $existingClient = Cliente::where('documento', $documento)->first();
        if ($existingClient) {
            return "Cliente con el documento $documento ya está registrado.";
        }

        $cliente = Cliente::create([
            'documento' => $documento,
            'nombres' => $nombres,
            'email' => $email,
            'celular' => $celular,
        ]);

        return "Cliente registrado con éxito: {$cliente->nombres}";
    }
}
