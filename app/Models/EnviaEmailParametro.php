<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnviaEmailParametro extends Model
{
    protected $table = 'envia_email_parametro';

    protected $fillable = [
        'email',
        'titulo',
        'mensagem',
        'status',
    ];
}
