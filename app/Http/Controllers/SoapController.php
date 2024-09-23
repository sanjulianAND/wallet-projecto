<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Wallet;
use App\Models\Transaccion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Helpers\ResponseHelper;

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
        try {
            $existingClient = Cliente::where('documento', $documento)->first();
            if ($existingClient) {
                return ResponseHelper::error("Cliente con el documento $documento ya está registrado.", '400');
            }

            $cliente = Cliente::create([
                'documento' => $documento,
                'nombres' => $nombres,
                'email' => $email,
                'celular' => $celular,
            ]);

            return ResponseHelper::success($cliente, "Cliente registrado con éxito.");
        } catch (\Exception $e) {
            return ResponseHelper::error("Error al registrar cliente: " . $e->getMessage(), '500');
        }
    }

    public function loadWallet($cliente_id, $monto)
    {
        try {
            $cliente = Cliente::find($cliente_id);

            if (!$cliente) {
                return ResponseHelper::error("Cliente no encontrado.", '404');
            }

            if ($monto <= 0) {
                return ResponseHelper::error("El monto debe ser mayor a cero.", '400');
            }

            $wallet = Wallet::firstOrCreate(['cliente_id' => $cliente_id]);

            $wallet->saldo += $monto;
            $wallet->save();

            return ResponseHelper::success("Recarga exitosa. Nuevo saldo: " . $wallet->saldo);
        } catch (\Exception $e) {
            return ResponseHelper::error("Error al recargar billetera: " . $e->getMessage(), '500');
        }
    }

    public function makePayment($cliente_id, $monto)
    {
        try {
            $cliente = Cliente::find($cliente_id);

            if (!$cliente) {
                return ResponseHelper::error("Cliente no encontrado.", '404');
            }

            $wallet = Wallet::where('cliente_id', $cliente_id)->first();

            if (!$wallet || $wallet->saldo < $monto) {
                return ResponseHelper::error("Saldo insuficiente.", '400');
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

            Mail::send('emails.token', ['cliente' => $cliente, 'monto' => $monto, 'token' => $token], function ($message) use ($cliente) {
                $message->to($cliente->email)
                    ->subject('Token de Confirmación de Pago');
            });

            return ResponseHelper::success("Token de confirmación generado. Id de sesión: $id_sesion.");
        } catch (\Exception $e) {
            return ResponseHelper::error("Error al generar el pago: " . $e->getMessage(), '500');
        }
    }

    public function confirmPayment($id_sesion, $token)
    {
        try {
            $transaccion = Transaccion::where('id_sesion', $id_sesion)->where('token', $token)->first();

            if (!$transaccion) {
                return ResponseHelper::error("Token o ID de sesión no válidos.", '404');
            }

            if ($transaccion->estado !== 'Pendiente') {
                return ResponseHelper::error("La transacción ya ha sido confirmada o cancelada.", '400');
            }

            $wallet = $transaccion->wallet;

            if ($wallet->saldo < $transaccion->monto) {
                return ResponseHelper::error("Saldo insuficiente en la billetera.", '400');
            }

            $wallet->saldo -= $transaccion->monto;
            $wallet->save();

            $transaccion->estado = 'Confirmado';
            $transaccion->save();

            return ResponseHelper::success("Pago confirmado con éxito. Nuevo saldo de la billetera: " . $wallet->saldo);
        } catch (\Exception $e) {
            return ResponseHelper::error("Error al confirmar el pago: " . $e->getMessage(), '500');
        }
    }

    public function checkBalance($cliente_id)
    {
        try {
            $cliente = Cliente::find($cliente_id);

            if (!$cliente) {
                return ResponseHelper::error("Cliente no encontrado.", '404');
            }

            $wallet = Wallet::where('cliente_id', $cliente_id)->first();

            if (!$wallet) {
                return ResponseHelper::error("Billetera no encontrada.", '404');
            }

            return ResponseHelper::success("Saldo actual de la billetera: " . $wallet->saldo);
        } catch (\Exception $e) {
            return ResponseHelper::error("Error al consultar el saldo: " . $e->getMessage(), '500');
        }
    }
}
