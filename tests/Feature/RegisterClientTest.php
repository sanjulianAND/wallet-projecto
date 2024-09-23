<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cliente;

class RegisterClientTest extends TestCase
{
    public function testRegisterClientSuccess()
    {
        $response = $this->soapRequest('registerClient', [
            'documento' => '1234567890',
            'nombres' => 'Juan Perez',
            'email' => 'juan@example.com',
            'celular' => '1234567890',
        ]);

        $response->assertSee('<success>true</success>');
        $response->assertSee('<cod_error>00</cod_error>');
        $response->assertSee('<data>Cliente registrado con éxito</data>');

        $this->assertDatabaseHas('clientes', [
            'documento' => '1234567890',
            'email' => 'juan@example.com',
        ]);
    }

    public function testRegisterClientDuplicate()
    {
        Cliente::factory()->create([
            'documento' => '1234567890',
            'email' => 'juan@example.com',
        ]);

        $response = $this->soapRequest('registerClient', [
            'documento' => '1234567890',
            'nombres' => 'Juan Perez',
            'email' => 'juan@example.com',
            'celular' => '1234567890',
        ]);

        $response->assertSee('<success>false</success>');
        $response->assertSee('<cod_error>400</cod_error>');
        $response->assertSee('<message_error>Cliente con el documento 1234567890 ya está registrado</message_error>');
    }

    private function soapRequest($action, $parameters)
    {
        $url = 'http://127.0.0.1:8000/soap-server';
        $client = new \SoapClient(null, ['location' => $url, 'uri' => $url]);

        return $client->__soapCall($action, [$parameters]);
    }
}
