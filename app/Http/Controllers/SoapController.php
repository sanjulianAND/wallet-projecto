<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Wallet;
use App\Models\Transaccion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

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

    public function makePayment($cliente_id, $monto)
    {
        $cliente = Cliente::find($cliente_id);

        if (!$cliente) {
            return "Cliente no encontrado.";
        }

        $wallet = Wallet::where('cliente_id', $cliente_id)->first();

        if (!$wallet || $wallet->saldo < $monto) {
            return "Saldo insuficiente.";
        }

        $token = Str::random(10);
        $id_sesion = Str::uuid();

        $transaccion = Transaccion::create([
            'cliente_id' => $cliente_id,
            'wallet_id' => $wallet->id,
            'monto' => $monto,
            'token' => $token,
            'estado' => 'Pendiente',
            'id_sesion' => $id_sesion,
        ]);

        /*Mail::send('emails.token', ['cliente' => $cliente, 'monto' => $monto, 'token' => $token], function ($message) use ($cliente) {
            $message->to($cliente->email)
                ->subject('Token de Confirmación de Pago');
        });*/

        return "Token de confirmación enviado a {$cliente->email}. Id de sesión: $id_sesion.";
    }

    public function confirmPayment($id_sesion, $token)
    {
        $transaccion = Transaccion::where('id_sesion', $id_sesion)->where('token', $token)->first();

        if (!$transaccion) {
            return "Token o ID de sesión no válidos.";
        }

        if ($transaccion->estado !== 'Pendiente') {
            return "La transacción ya ha sido confirmada o cancelada.";
        }

        $wallet = $transaccion->wallet;

        if ($wallet->saldo < $transaccion->monto) {
            return "Saldo insuficiente en la billetera.";
        }

        $wallet->saldo -= $transaccion->monto;
        $wallet->save();

        $transaccion->estado = 'Confirmado';
        $transaccion->save();

        return "Pago confirmado con éxito. Nuevo saldo de la billetera: " . $wallet->saldo;
    }
    public function checkBalance($cliente_id)
    {
        $cliente = Cliente::find($cliente_id);

        if (!$cliente) {
            return "Cliente no encontrado.";
        }

        $wallet = Wallet::where('cliente_id', $cliente_id)->first();

        if (!$wallet) {
            return "Billetera no encontrada.";
        }

        return "Saldo actual de la billetera: " . $wallet->saldo;
    }
}
