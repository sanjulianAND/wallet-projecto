<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaccionesTable extends Migration
{
    public function up()
    {
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade');
            $table->decimal('monto', 10, 2);
            $table->string('token')->unique();
            $table->string('estado')->default('Pendiente');
            $table->string('id_sesion')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transacciones');
    }
}
