<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produto extends Model
{
    protected $table = 'produtos';

    public const CREATED_AT = 'criado_em';

    public const UPDATED_AT = 'atualizado_em';

    protected $fillable = ['nome', 'descricao', 'tipo', 'categoria', 'preco', 'ativo', 'duracao_minutos', 'estoque'];

    protected $casts = ['preco' => 'decimal:2', 'ativo' => 'boolean'];

    public function agendamentos(): BelongsToMany
    {
        return $this->belongsToMany(Agendamento::class, 'itens_agendamento')->withPivot(['quantidade', 'preco_unitario'])->withTimestamps('criado_em', 'atualizado_em');
    }
}
