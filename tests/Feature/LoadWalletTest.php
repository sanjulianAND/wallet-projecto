<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cliente;
use App\Models\Wallet;

class LoadWalletTest extends TestCase
{
    public function testLoadWalletSuccess()
    {
        $cliente = Cliente::factory()->create();
        $wallet = Wallet::factory()->create([
            'cliente_id' => $cliente->id,
            'saldo' => 200.00,
        ]);

        $response = $this->soapRequest('loadWallet', [
            'cliente_id' => $cliente->id,
            'monto' => 100.00,
        ]);

        $response->assertSee('<success>true</success>');
        $response->assertSee('<cod_error>00</cod_error>');
        $response->assertSee('<data>Recarga exitosa. Nuevo saldo: 300.00</data>');

        $this->assertDatabaseHas('wallets', [
            'cliente_id' => $cliente->id,
            'saldo' => 300.00,
        ]);
    }

    public function testLoadWalletClienteNotFound()
    {
        $response = $this->soapRequest('loadWallet', [
            'cliente_id' => 99999, // ID que no existe
            'monto' => 100.00,
        ]);

        $response->assertSee('<success>false</success>');
        $response->assertSee('<cod_error>404</cod_error>');
        $response->assertSee('<message_error>Cliente no encontrado</message_error>');
    }

    public function testLoadWalletInvalidAmount()
    {
        $cliente = Cliente::factory()->create();

        $response = $this->soapRequest('loadWallet', [
            'cliente_id' => $cliente->id,
            'monto' => -50.00,
        ]);

        $response->assertSee('<success>false</success>');
        $response->assertSee('<cod_error>400</cod_error>');
        $response->assertSee('<message_error>El monto debe ser mayor a cero</message_error>');
    }

    private function soapRequest($action, $parameters)
    {
        $url = 'http://127.0.0.1:8000/soap-server';
        $client = new \SoapClient(null, ['location' => $url, 'uri' => $url]);

        return $client->__soapCall($action, [$parameters]);
    }
}
