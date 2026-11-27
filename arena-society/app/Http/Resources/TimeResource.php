<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'nome' => $this->nome, 'categoria' => $this->categoria, 'nome_capitao' => $this->nome_capitao, 'telefone' => $this->telefone, 'criado_em' => $this->criado_em];
    }
}
