@extends('layouts.app')
@section('title', 'Arena Society | Painel')
@section('content')
<h1>Painel da Arena</h1>
<div class="grid">
    <section class="card"><h2>Cadastrar time</h2><form method="post" action="{{ route('painel.times.store') }}">@csrf
        <input name="nome" placeholder="Nome da equipe" required><input name="categoria" list="categorias-times" placeholder="Categoria" required>
        <datalist id="categorias-times">@foreach($categoriasTimes as $categoria)<option value="{{ $categoria }}">@endforeach</datalist>
        <input name="nome_capitao" placeholder="Nome do capitão" required><input name="telefone" placeholder="Telefone" required><button>Salvar equipe</button>
    </form></section>
    <section class="card"><h2>Novo produto ou serviço</h2><form method="post" action="{{ route('painel.produtos.store') }}">@csrf
        <input name="nome" placeholder="Nome" required><input name="categoria" list="categorias-produtos" placeholder="Categoria" required>
        <datalist id="categorias-produtos">@foreach($categoriasProdutos as $categoria)<option value="{{ $categoria }}">@endforeach</datalist>
        <select name="tipo"><option value="servico">Serviço</option><option value="produto">Produto</option></select>
        <input name="preco" type="number" min="0.01" step="0.01" placeholder="Preço" required>
        <input name="duracao_minutos" type="number" min="1" placeholder="Duração (serviço)">
        <input name="estoque" type="number" min="0" placeholder="Estoque (produto)">
        <input name="descricao" placeholder="Descrição"><button>Adicionar ao catálogo</button>
    </form></section>
</div>
<section class="card"><h2>Times cadastrados</h2>
    <form method="get"><input name="busca" value="{{ request('busca') }}" placeholder="Buscar nome ou telefone"><button>Buscar</button></form>
    <table><tr><th>Equipe</th><th>Categoria</th><th>Capitão</th><th>Telefone</th><th></th></tr>
    @foreach($times as $time)<tr><td>{{ $time->nome }}</td><td>{{ $time->categoria }}</td><td>{{ $time->nome_capitao }}</td><td>{{ $time->telefone }}</td><td><details><summary>Editar</summary><form method="post" action="{{ route('painel.times.update', $time) }}">@csrf @method('put')<input name="nome" value="{{ $time->nome }}" required><input name="categoria" list="categorias-times" value="{{ $time->categoria }}" required><input name="nome_capitao" value="{{ $time->nome_capitao }}" required><input name="telefone" value="{{ $time->telefone }}" required><button>Salvar</button></form></details><form method="post" action="{{ route('painel.times.delete', $time) }}">@csrf @method('delete')<button>Excluir</button></form></td></tr>@endforeach
    </table>{{ $times->links() }}
</section>
<section class="card"><h2>Catálogo</h2>
    <form method="get"><select name="tipo"><option value="">Todos os tipos</option><option value="servico">Serviços</option><option value="produto">Produtos</option></select><input name="categoria" list="categorias-produtos" placeholder="Categoria"><select name="ordenar"><option value="nome">Nome</option><option value="preco">Preço</option><option value="preco_desc">Maior preço</option></select><button>Filtrar</button></form>
    <table><tr><th>Item</th><th>Tipo</th><th>Categoria</th><th>Preço</th><th></th></tr>
    @foreach($produtos as $produto)<tr><td>{{ $produto->nome }}</td><td>{{ $produto->tipo === 'servico' ? 'Serviço' : 'Produto' }}</td><td>{{ $produto->categoria }}</td><td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td><td><details><summary>Editar</summary><form method="post" action="{{ route('painel.produtos.update', $produto) }}">@csrf @method('put')<input name="nome" value="{{ $produto->nome }}" required><input name="descricao" value="{{ $produto->descricao }}"><input name="categoria" list="categorias-produtos" value="{{ $produto->categoria }}" required><select name="tipo"><option value="servico" @selected($produto->tipo === 'servico')>Serviço</option><option value="produto" @selected($produto->tipo === 'produto')>Produto</option></select><input name="preco" type="number" min="0.01" step="0.01" value="{{ $produto->preco }}" required><input name="duracao_minutos" type="number" min="1" value="{{ $produto->duracao_minutos }}"><input name="estoque" type="number" min="0" value="{{ $produto->estoque }}"><input type="hidden" name="ativo" value="0"><label><input type="checkbox" name="ativo" value="1" @checked($produto->ativo)>Ativo</label><button>Salvar</button></form></details><form method="post" action="{{ route('painel.produtos.delete', $produto) }}">@csrf @method('delete')<button>Excluir</button></form></td></tr>@endforeach
    </table>{{ $produtos->links() }}
</section>
<section class="card"><h2>Agendar quadra</h2><form method="post" action="{{ route('painel.agendamentos.store') }}">@csrf
    <select name="time_id" required><option value="">Selecione o time</option>@foreach($timesDisponiveis as $time)<option value="{{ $time->id }}">{{ $time->nome }} — {{ $time->categoria }}</option>@endforeach</select>
    <select name="quadra"><option>Quadra 1</option><option>Quadra 2</option></select><input type="datetime-local" name="inicio_em" required>
    <select name="duracao_horas"><option value="1">1 hora</option><option value="2">2 horas</option></select>
    <p>Selecione os extras (a locação da quadra entra automaticamente):</p>
    @foreach($produtosDisponiveis->where('categoria', '!=', 'Quadra') as $produto)
        <label><input class="extra-check" type="checkbox" name="itens[{{ $loop->index }}][produto_id]" value="{{ $produto->id }}">{{ $produto->nome }} — R$ {{ number_format($produto->preco, 2, ',', '.') }}</label>
        <input class="extra-quantidade" type="number" name="itens[{{ $loop->index }}][quantidade]" min="1" value="1" disabled aria-label="Quantidade de {{ $produto->nome }}">
    @endforeach
    <input name="observacoes" placeholder="Observações"><button>Confirmar reserva</button>
</form></section>
<section class="card"><h2>Próximas reservas</h2><table><tr><th>Equipe</th><th>Quadra</th><th>Data</th><th>Duração</th><th>Status</th><th>Total</th><th>Extras</th><th>Ação</th></tr>
    @forelse($agendamentos as $agendamento)<tr><td>{{ $agendamento->time->nome }}</td><td>{{ $agendamento->quadra }}</td><td>{{ $agendamento->inicio_em->format('d/m/Y H:i') }}</td><td>{{ $agendamento->duracao_horas }}h</td><td>{{ $agendamento->status }}</td><td>R$ {{ number_format($agendamento->total, 2, ',', '.') }}</td><td>{{ $agendamento->produtos->pluck('nome')->join(', ') }}</td><td><form method="post" action="{{ route('painel.agendamentos.status', $agendamento) }}">@csrf @method('patch')<input type="hidden" name="status" value="cancelado"><button>Cancelar</button></form></td></tr>
    @empty<tr><td colspan="8">Nenhuma reserva registrada.</td></tr>@endforelse
</table></section>
<script>document.querySelectorAll('.extra-check').forEach((checkbox) => { const quantidade = checkbox.closest('label').nextElementSibling; checkbox.addEventListener('change', () => { quantidade.disabled = !checkbox.checked; }); });</script>
@endsection

