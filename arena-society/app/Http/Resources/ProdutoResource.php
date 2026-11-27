<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdutoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'nome' => $this->nome, 'descricao' => $this->descricao, 'tipo' => $this->tipo, 'categoria' => $this->categoria, 'preco' => $this->preco, 'ativo' => $this->ativo, 'duracao_minutos' => $this->duracao_minutos, 'estoque' => $this->estoque];
    }
}
