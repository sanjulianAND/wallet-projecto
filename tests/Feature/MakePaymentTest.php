<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cliente;
use App\Models\Wallet;
use App\Models\Transaccion;

class MakePaymentTest extends TestCase
{
    public function testMakePaymentSuccess()
    {
        $cliente = Cliente::factory()->create();
        $wallet = Wallet::factory()->create([
            'cliente_id' => $cliente->id,
            'saldo' => 500.00,
        ]);

        $response = $this->soapRequest('makePayment', [
            'cliente_id' => $cliente->id,
            'monto' => 100.00,
        ]);

        $response->assertSee('<success>true</success>');
        $response->assertSee('<cod_error>00</cod_error>');
        $response->assertSee('<data>Token de confirmación generado</data>');
    }


    public function testMakePaymentInsufficientBalance()
    {
        $cliente = Cliente::factory()->create();
        $wallet = Wallet::factory()->create([
            'cliente_id' => $cliente->id,
            'saldo' => 50.00,
        ]);

        $response = $this->soapRequest('makePayment', [
            'cliente_id' => $cliente->id,
            'monto' => 100.00,
        ]);

        $response->assertSee('<success>false</success>');
        $response->assertSee('<cod_error>400</cod_error>');
        $response->assertSee('<message_error>Saldo insuficiente</message_error>');
    }

    private function soapRequest($action, $parameters)
    {
        $url = 'http://127.0.0.1:8000/soap-server';
        $client = new \SoapClient(null, ['location' => $url, 'uri' => $url]);

        return $client->__soapCall($action, [$parameters]);
    }
}
