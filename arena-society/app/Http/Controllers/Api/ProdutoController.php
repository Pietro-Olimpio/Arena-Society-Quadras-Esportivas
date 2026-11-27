<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalvarProdutoRequest;
use App\Http\Resources\ProdutoResource;
use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index(Request $r)
    {
        return ProdutoResource::collection(Produto::query()->where('ativo', true)->when($r->tipo, fn ($q, $v) => $q->where('tipo', $v))->when($r->categoria, fn ($q, $v) => $q->where('categoria', $v))->orderBy($r->query('ordenar') === 'preco_desc' ? 'preco' : 'nome', $r->query('ordenar') === 'preco_desc' ? 'desc' : 'asc')->paginate(12));
    }

    public function store(SalvarProdutoRequest $r)
    {
        return (new ProdutoResource(Produto::create($r->validated())))->response()->setStatusCode(201);
    }

    public function show(Produto $produto)
    {
        return new ProdutoResource($produto);
    }

    public function update(SalvarProdutoRequest $r, Produto $produto)
    {
        $produto->update($r->validated());

        return new ProdutoResource($produto->refresh());
    }

    public function destroy(Produto $produto)
    {
        $produto->delete();

        return response()->noContent();
    }
}
