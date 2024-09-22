<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    // Atributos que se pueden asignar masivamente.
    protected $fillable = ['documento', 'nombres', 'email', 'celular'];
}
