<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgendamentoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'time' => new TimeResource($this->whenLoaded('time')), 'quadra' => $this->quadra, 'inicio_em' => $this->inicio_em?->toIso8601String(), 'duracao_horas' => $this->duracao_horas, 'status' => $this->status, 'total' => $this->total, 'itens' => $this->whenLoaded('produtos')->map(fn ($p) => ['id' => $p->id, 'nome' => $p->nome, 'quantidade' => $p->pivot->quantidade, 'preco_unitario' => $p->pivot->preco_unitario]), 'observacoes' => $this->observacoes];
    }
}
