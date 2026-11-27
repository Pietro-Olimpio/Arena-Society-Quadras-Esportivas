<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalvarAgendamentoRequest;
use App\Http\Resources\AgendamentoResource;
use App\Models\Agendamento;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AgendamentoController extends Controller
{
    public function index()
    {
        return AgendamentoResource::collection(Agendamento::with(['time', 'produtos'])->latest('inicio_em')->paginate(10));
    }

    public function show(Agendamento $agendamento)
    {
        return new AgendamentoResource($agendamento->load(['time', 'produtos']));
    }

    public function store(SalvarAgendamentoRequest $r)
    {
        $data = $r->validated();
        $agendamento = DB::transaction(function () use ($data) {
            $start = Carbon::parse($data['inicio_em']);
            $end = $start->copy()->addHours($data['duracao_horas']);
            if (DB::getDriverName() === 'pgsql') {
                DB::select('SELECT pg_advisory_xact_lock(hashtext(?))', [$data['quadra']]);
            }
            $candidates = Agendamento::where('quadra', $data['quadra'])->whereIn('status', ['pendente', 'confirmado'])->where('inicio_em', '>=', $start->copy()->subHours(2))->where('inicio_em', '<', $end)->lockForUpdate()->get();
            foreach ($candidates as $old) {
                $oldEnd = $old->inicio_em->copy()->addHours($old->duracao_horas);
                if ($start->lt($oldEnd) && $end->gt($old->inicio_em)) {
                    throw ValidationException::withMessages(['inicio_em' => 'A quadra já está reservada nesse intervalo.']);
                }
            }
            $agendamento = Agendamento::create(['time_id' => $data['time_id'], 'quadra' => $data['quadra'], 'inicio_em' => $start, 'duracao_horas' => $data['duracao_horas'], 'status' => 'pendente', 'observacoes' => $data['observacoes'] ?? null, 'total' => 0]);
            $itens = [];
            $locacaoQuadra = Produto::where('ativo', true)->where('categoria', 'Quadra')->where('tipo', 'servico')->first();
            if (! $locacaoQuadra) {
                throw ValidationException::withMessages(['quadra' => 'A locação de quadra não está cadastrada no catálogo.']);
            }
            $itens[$locacaoQuadra->id] = ['quantidade' => $data['duracao_horas'], 'preco_unitario' => $locacaoQuadra->preco];
            $total = (float) $locacaoQuadra->preco * $data['duracao_horas'];
            foreach ($data['itens'] ?? [] as $item) {
                $produto = Produto::where('ativo', true)->findOrFail($item['produto_id']);
                if ($produto->id === $locacaoQuadra->id) {
                    throw ValidationException::withMessages(['itens' => 'A locação da quadra já é incluída automaticamente.']);
                } $qty = $item['quantidade'];
                if ($produto->tipo === 'produto' && $produto->estoque < $qty) {
                    throw ValidationException::withMessages(['itens' => 'Estoque insuficiente para '.$produto->nome.'.']);
                } $itens[$produto->id] = ['quantidade' => $qty, 'preco_unitario' => $produto->preco];
                $total += (float) $produto->preco * $qty;
                if ($produto->tipo === 'produto') {
                    $produto->decrement('estoque', $qty);
                }
            }
            $agendamento->produtos()->attach($itens);
            $agendamento->update(['total' => $total]);

            return $agendamento;
        });

        return (new AgendamentoResource($agendamento->load(['time', 'produtos'])))->response()->setStatusCode(201);
    }

    public function updateStatus(Request $r, Agendamento $agendamento)
    {
        $data = $r->validate(['status' => 'required|in:pendente,confirmado,concluido,cancelado']);
        DB::transaction(function () use ($agendamento, $data) {
            $agendamento->load('produtos');
            if ($data['status'] === 'cancelado' && $agendamento->status !== 'cancelado') {
                foreach ($agendamento->produtos as $produto) {
                    if ($produto->tipo === 'produto') {
                        $produto->increment('estoque', $produto->pivot->quantidade);
                    }
                }
            }
            $agendamento->update($data);
        });

        return new AgendamentoResource($agendamento->load(['time', 'produtos']));
    }

    public function destroy(Agendamento $agendamento)
    {
        $this->updateStatus(request()->merge(['status' => 'cancelado']), $agendamento);

        return response()->noContent();
    }
}
