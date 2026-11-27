<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Time extends Model
{
    protected $table = 'times';

    public const CREATED_AT = 'criado_em';

    public const UPDATED_AT = 'atualizado_em';

    protected $fillable = ['nome', 'categoria', 'nome_capitao', 'telefone'];

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }
}
