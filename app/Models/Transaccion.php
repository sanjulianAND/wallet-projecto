<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'wallet_id',
        'monto',
        'token',
        'estado',
        'id_sesion',
    ];

    protected $table = 'transacciones';

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
