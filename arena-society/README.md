# Arena Society — Arena Gol Society

Projeto integrador Back-End em Laravel 13 para administrar times, catálogo de itens e reservas de quadras. Desenvolvido pelo grupo **Pietro, F. Scalfi e Murilo**, com base nos exercícios de PHP e MVC do repositório [Pietro-Olimpio/back-end](https://github.com/Pietro-Olimpio/back-end). O repositório de referência contém material de aulas de PHP puro (`AULAS` e `PHPVanilla`), não uma aplicação Laravel pronta; este diretório é a implementação Laravel do cenário 14.

## Regras implementadas

- Times armazenam equipe, categoria, capitão e telefone único validado.
- Catálogo diferencia produtos e serviços, preço positivo, categoria, estado ativo, estoque ou duração.
- Reserva associa time e itens; a locação de quadra é somada automaticamente conforme a duração (1h ou 2h), junto aos extras.
- Datas passadas e horários sobrepostos para a mesma quadra são rejeitados. Reservas pendentes e confirmadas ocupam o horário.
- Quantidades de estoque são verificadas e baixadas dentro da transação de reserva.
- API usa Sanctum e `auth:sanctum`; listagens paginadas carregam relações para reduzir consultas N+1.

## Nomes do sistema

O domínio usa nomes em português no código, na API e no PostgreSQL: tabelas `times`, `produtos`, `agendamentos` e `itens_agendamento`, com campos como `nome`, `preco`, `inicio_em` e `quantidade`. Uma migration renomeia a estrutura existente sem apagar os registros. As tabelas e campos de autenticação e infraestrutura (`users`, `email`, `password`, tabelas do Sanctum) continuam com os nomes padrão do Laravel.

## Requisitos e instalação

Requer PHP 8.3+, Composer e PostgreSQL 16+.

```bash
cd arena-society
composer install
cp .env.example .env
```

No Windows PowerShell, use `Copy-Item .env.example .env` no lugar de `cp`. Configure `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` e `SENHA_ADMIN_SEMEADURA` no `.env` local. Use uma senha sua para o usuário local de demonstração; o seeder não contém senha padrão. Em seguida:

```bash
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Acesse `http://127.0.0.1:8000`. O seeder cria o usuário local `admin@arenasociety` usando a senha definida em `SENHA_ADMIN_SEMEADURA`. Não compartilhe nem publique essa senha.

## Interface Web

O painel Blade (`/login` e `/`) permite entrar com a conta administrativa, cadastrar, editar, pesquisar e excluir times e itens do catálogo, filtrar catálogo, criar agendamentos e cancelar agendamentos. Formulários usam CSRF, validação via Form Requests e saída Blade escapada. O painel exige sessão autenticada.

## API

Todas as rotas, exceto `POST /api/login`, exigem `Authorization: Bearer <token>`. JSON de validação retorna `422`; criação retorna `201`; ausência de autenticação retorna `401`; remoção retorna `204`.

| Método | Endpoint | Descrição |
|---|---|---|
| POST | `/api/login` | E-mail e senha; retorna token Sanctum |
| POST | `/api/logout` | Revoga token atual |
| GET, POST | `/api/times` | Listar (busca `?busca=`) e cadastrar times |
| GET, PUT/PATCH, DELETE | `/api/times/{time}` | Consultar, atualizar e remover time |
| GET, POST | `/api/produtos` | Catálogo; filtros `tipo`, `categoria`, `ordenar=preco_desc`; criar item |
| GET, PUT/PATCH, DELETE | `/api/produtos/{produto}` | Consultar, atualizar e remover item |
| GET, POST | `/api/agendamentos` | Listar e criar reservas |
| GET, DELETE | `/api/agendamentos/{agendamento}` | Consultar ou cancelar reserva |
| PATCH | `/api/agendamentos/{agendamento}/status` | Mudar para `pendente`, `confirmado`, `concluido` ou `cancelado` |

### Exemplos

Login:

```json
POST /api/login
{"email":"admin@arenasociety.local","password":"SUA_SENHA_LOCAL"}
```

Reserva com bola e arbitragem (IDs disponíveis em `GET /api/produtos`):

```json
POST /api/agendamentos
{
  "time_id": 1,
  "quadra": "Quadra 1",
  "inicio_em": "2027-01-15T20:00:00-03:00",
  "duracao_horas": 2,
  "itens": [{"produto_id": 4, "quantidade": 1}, {"produto_id": 2, "quantidade": 1}]
}
```

A locação da quadra é adicionada automaticamente. IDs e horário do exemplo precisam estar válidos para os dados e relógio local.

## Postman

Importe [`postman/Arena-Society.postman_collection.json`](postman/Arena-Society.postman_collection.json). Informe sua senha local na variável de coleção `password` e faça login primeiro; o script salva o token na variável de coleção `token`. Depois use “Listar times”, “Listar catálogo” e “Criar reserva”.

## Estrutura principal

- `app/Models`: Time, Produto, Agendamento e User.
- `app/Http/Requests`: validação em português.
- `app/Http/Resources`: formato de resposta JSON.
- `database/migrations`: tabelas e chaves estrangeiras.
- `database/seeders`: dados demonstrativos e conta local.
- `routes/web.php` e `routes/api.php`: interface e API.
