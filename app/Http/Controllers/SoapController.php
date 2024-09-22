<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Wallet;

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

    public function loadWallet($cliente_id, $monto)
    {
        $cliente = Cliente::find($cliente_id);

        if (!$cliente) {
            return "Cliente no encontrado.";
        }

        if ($monto <= 0) {
            return "El monto debe ser mayor a cero.";
        }

        $wallet = Wallet::firstOrCreate(['cliente_id' => $cliente_id]);

        $wallet->saldo += $monto;
        $wallet->save();

        return "Recarga exitosa. Nuevo saldo: " . $wallet->saldo;
    }
}
