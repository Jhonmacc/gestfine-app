<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    use HasFactory;

    protected $table = 'parametros';

    protected $fillable = [
        'dias_faltantes',
        'texto',
        'valor',
        'observacao',
    ];
}
