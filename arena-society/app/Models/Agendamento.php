<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Agendamento extends Model
{
    protected $table = 'agendamentos';

    public const CREATED_AT = 'criado_em';

    public const UPDATED_AT = 'atualizado_em';

    protected $fillable = ['time_id', 'quadra', 'inicio_em', 'duracao_horas', 'status', 'total', 'observacoes'];

    protected $casts = ['inicio_em' => 'datetime', 'total' => 'decimal:2'];

    public function time(): BelongsTo
    {
        return $this->belongsTo(Time::class);
    }

    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'itens_agendamento')->withPivot(['quantidade', 'preco_unitario'])->withTimestamps('criado_em', 'atualizado_em');
    }
}
