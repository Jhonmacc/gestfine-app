<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;

    protected $fillable =
    [
    'name',
    'email',
    'validTo_time_t',
    'cnpj_cpf',
    'societario',
    'tipo_integrante ',
    'numero',
    'certificate_path',
    ];

}
