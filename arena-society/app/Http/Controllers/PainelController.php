<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\AgendamentoController;
use App\Http\Requests\SalvarAgendamentoRequest;
use App\Http\Requests\SalvarProdutoRequest;
use App\Http\Requests\SalvarTimeRequest;
use App\Models\Agendamento;
use App\Models\Produto;
use App\Models\Time;
use Illuminate\Http\Request;

class PainelController extends Controller
{
    public function cadastrarTime(SalvarTimeRequest $request)
    {
        Time::create($request->validated());

        return back()->with('success', 'Time cadastrado.');
    }

    public function atualizarTime(SalvarTimeRequest $request, Time $time)
    {
        $time->update($request->validated());

        return back()->with('success', 'Time atualizado.');
    }

    public function excluirTime(Time $time)
    {
        $time->delete();

        return back()->with('success', 'Time removido.');
    }

    public function cadastrarProduto(SalvarProdutoRequest $request)
    {
        Produto::create($request->validated());

        return back()->with('success', 'Item cadastrado.');
    }

    public function atualizarProduto(SalvarProdutoRequest $request, Produto $produto)
    {
        $produto->update($request->validated());

        return back()->with('success', 'Item atualizado.');
    }

    public function excluirProduto(Produto $produto)
    {
        $produto->delete();

        return back()->with('success', 'Item removido.');
    }

    public function cadastrarAgendamento(SalvarAgendamentoRequest $request)
    {
        app(AgendamentoController::class)->store($request);

        return redirect()->route('painel')->with('success', 'Agendamento criado e total calculado.');
    }

    public function atualizarStatusAgendamento(Request $request, Agendamento $agendamento)
    {
        $data = $request->validate(['status' => ['required', 'in:pendente,confirmado,concluido,cancelado']]);
        if ($data['status'] === 'cancelado' && $agendamento->status !== 'cancelado') {
            foreach ($agendamento->produtos as $produto) {
                if ($produto->tipo === 'produto') {
                    $produto->increment('estoque', $produto->pivot->quantidade);
                }
            }
        }
        $agendamento->update($data);

        return back()->with('success', 'Status da reserva atualizado.');
    }

    public function index(Request $request)
    {
        $times = Time::withCount('agendamentos')->when($request->busca, fn ($q, $s) => $q->where('nome', 'ilike', '%'.$s.'%')->orWhere('telefone', 'ilike', '%'.$s.'%'))->orderBy('nome')->paginate(8, ['*'], 'times');
        $ordem = $request->ordenar === 'preco_desc' ? 'desc' : 'asc';
        $colunaOrdenacao = str_starts_with($request->ordenar ?? '', 'preco') ? 'preco' : 'nome';
        $produtos = Produto::query()->when($request->tipo, fn ($q, $t) => $q->where('tipo', $t))->when($request->categoria, fn ($q, $c) => $q->where('categoria', $c))->orderBy($colunaOrdenacao, $ordem)->paginate(8, ['*'], 'produtos');
        $agendamentos = Agendamento::with(['time', 'produtos'])->latest('inicio_em')->limit(10)->get();
        $timesDisponiveis = Time::orderBy('nome')->get();
        $produtosDisponiveis = Produto::where('ativo', true)->orderBy('nome')->get();
        $categoriasTimes = Time::query()->whereNotNull('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        $categoriasProdutos = Produto::query()->whereNotNull('categoria')->distinct()->orderBy('categoria')->pluck('categoria');

        return view('painel', compact('times', 'produtos', 'agendamentos', 'timesDisponiveis', 'produtosDisponiveis', 'categoriasTimes', 'categoriasProdutos'));
    }
}
