<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cliente;
use App\Models\Wallet;

class CheckBalanceTest extends TestCase
{
    public function testCheckBalanceSuccess()
    {
        $cliente = Cliente::factory()->create();
        $wallet = Wallet::factory()->create([
            'cliente_id' => $cliente->id,
            'saldo' => 500.00,
        ]);

        $response = $this->soapRequest('checkBalance', [
            'cliente_id' => $cliente->id,
        ]);

        $response->assertSee('<success>true</success>');
        $response->assertSee('<cod_error>00</cod_error>');
        $response->assertSee('<data>Saldo actual de la billetera: 500.00</data>');
    }

    public function testCheckBalanceClienteNotFound()
    {
        $response = $this->soapRequest('checkBalance', [
            'cliente_id' => 99999,
        ]);

        $response->assertSee('<success>false</success>');
        $response->assertSee('<cod_error>404</cod_error>');
        $response->assertSee('<message_error>Cliente no encontrado</message_error>');
    }

    private function soapRequest($action, $parameters)
    {
        $url = 'http://127.0.0.1:8000/soap-server';
        $client = new \SoapClient(null, ['location' => $url, 'uri' => $url]);

        return $client->__soapCall($action, [$parameters]);
    }
}
