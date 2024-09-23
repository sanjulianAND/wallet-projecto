<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cliente;
use App\Models\Wallet;
use App\Models\Transaccion;

class ConfirmPaymentTest extends TestCase
{
    public function testConfirmPaymentSuccess()
    {
        $cliente = Cliente::factory()->create();
        $wallet = Wallet::factory()->create([
            'cliente_id' => $cliente->id,
            'saldo' => 500.00,
        ]);
        $transaccion = Transaccion::factory()->create([
            'cliente_id' => $cliente->id,
            'wallet_id' => $wallet->id,
            'monto' => 100.00,
            'token' => 'testToken123',
            'estado' => 'Pendiente',
            'id_sesion' => 'testSession123',
        ]);

        $response = $this->soapRequest('confirmPayment', [
            'id_sesion' => $transaccion->id_sesion,
            'token' => $transaccion->token,
        ]);

        $response->assertSee('<success>true</success>');
        $response->assertSee('<cod_error>00</cod_error>');
        $response->assertSee('<data>Pago confirmado con éxito</data>');
    }

    public function testConfirmPaymentInvalidToken()
    {
        $cliente = Cliente::factory()->create();
        $transaccion = Transaccion::factory()->create([
            'cliente_id' => $cliente->id,
            'token' => 'validToken',
            'estado' => 'Pendiente',
            'id_sesion' => 'validSession',
        ]);

        $response = $this->soapRequest('confirmPayment', [
            'id_sesion' => $transaccion->id_sesion,
            'token' => 'invalidToken',
        ]);

        $response->assertSee('<success>false</success>');
        $response->assertSee('<cod_error>400</cod_error>');
        $response->assertSee('<message_error>Token o ID de sesión no válidos</message_error>');
    }

    private function soapRequest($action, $parameters)
    {
        $url = 'http://127.0.0.1:8000/soap-server';
        $client = new \SoapClient(null, ['location' => $url, 'uri' => $url]);

        return $client->__soapCall($action, [$parameters]);
    }
}
