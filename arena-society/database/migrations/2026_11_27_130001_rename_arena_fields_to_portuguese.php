<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('teams', 'times');
        Schema::rename('products', 'produtos');
        Schema::rename('bookings', 'agendamentos');
        Schema::rename('booking_items', 'itens_agendamento');

        Schema::table('times', function (Blueprint $table): void {
            $table->renameColumn('name', 'nome');
            $table->renameColumn('category', 'categoria');
            $table->renameColumn('captain_name', 'nome_capitao');
            $table->renameColumn('phone', 'telefone');
            $table->renameColumn('created_at', 'criado_em');
            $table->renameColumn('updated_at', 'atualizado_em');
        });

        Schema::table('produtos', function (Blueprint $table): void {
            $table->renameColumn('name', 'nome');
            $table->renameColumn('description', 'descricao');
            $table->renameColumn('type', 'tipo');
            $table->renameColumn('category', 'categoria');
            $table->renameColumn('price', 'preco');
            $table->renameColumn('active', 'ativo');
            $table->renameColumn('duration_minutes', 'duracao_minutos');
            $table->renameColumn('stock', 'estoque');
            $table->renameColumn('created_at', 'criado_em');
            $table->renameColumn('updated_at', 'atualizado_em');
        });

        Schema::table('agendamentos', function (Blueprint $table): void {
            $table->renameColumn('team_id', 'time_id');
            $table->renameColumn('court', 'quadra');
            $table->renameColumn('starts_at', 'inicio_em');
            $table->renameColumn('duration_hours', 'duracao_horas');
            $table->renameColumn('notes', 'observacoes');
            $table->renameColumn('created_at', 'criado_em');
            $table->renameColumn('updated_at', 'atualizado_em');
        });

        Schema::table('itens_agendamento', function (Blueprint $table): void {
            $table->renameColumn('booking_id', 'agendamento_id');
            $table->renameColumn('product_id', 'produto_id');
            $table->renameColumn('quantity', 'quantidade');
            $table->renameColumn('unit_price', 'preco_unitario');
            $table->renameColumn('created_at', 'criado_em');
            $table->renameColumn('updated_at', 'atualizado_em');
        });

        if (DB::getDriverName() === 'pgsql') {
            $restricoes = DB::select("SELECT conname FROM pg_constraint WHERE conrelid = to_regclass('produtos') AND contype = 'c' AND pg_get_constraintdef(oid) ILIKE '%tipo%'");
            foreach ($restricoes as $restricao) {
                $nome = str_replace('"', '""', $restricao->conname);
                DB::statement('ALTER TABLE produtos DROP CONSTRAINT "'.$nome.'"');
            }

            DB::table('produtos')->where('tipo', 'service')->update(['tipo' => 'servico']);
            DB::table('produtos')->where('tipo', 'product')->update(['tipo' => 'produto']);
            DB::statement("ALTER TABLE produtos ADD CONSTRAINT produtos_tipo_check CHECK (tipo IN ('servico', 'produto'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE produtos DROP CONSTRAINT IF EXISTS produtos_tipo_check');
            DB::table('produtos')->where('tipo', 'servico')->update(['tipo' => 'service']);
            DB::table('produtos')->where('tipo', 'produto')->update(['tipo' => 'product']);
            DB::statement("ALTER TABLE produtos ADD CONSTRAINT products_type_check CHECK (tipo IN ('service', 'product'))");
        }

        Schema::table('itens_agendamento', function (Blueprint $table): void {
            $table->renameColumn('agendamento_id', 'booking_id');
            $table->renameColumn('produto_id', 'product_id');
            $table->renameColumn('quantidade', 'quantity');
            $table->renameColumn('preco_unitario', 'unit_price');
            $table->renameColumn('criado_em', 'created_at');
            $table->renameColumn('atualizado_em', 'updated_at');
        });

        Schema::table('agendamentos', function (Blueprint $table): void {
            $table->renameColumn('time_id', 'team_id');
            $table->renameColumn('quadra', 'court');
            $table->renameColumn('inicio_em', 'starts_at');
            $table->renameColumn('duracao_horas', 'duration_hours');
            $table->renameColumn('observacoes', 'notes');
            $table->renameColumn('criado_em', 'created_at');
            $table->renameColumn('atualizado_em', 'updated_at');
        });

        Schema::table('produtos', function (Blueprint $table): void {
            $table->renameColumn('nome', 'name');
            $table->renameColumn('descricao', 'description');
            $table->renameColumn('tipo', 'type');
            $table->renameColumn('categoria', 'category');
            $table->renameColumn('preco', 'price');
            $table->renameColumn('ativo', 'active');
            $table->renameColumn('duracao_minutos', 'duration_minutes');
            $table->renameColumn('estoque', 'stock');
            $table->renameColumn('criado_em', 'created_at');
            $table->renameColumn('atualizado_em', 'updated_at');
        });

        Schema::table('times', function (Blueprint $table): void {
            $table->renameColumn('nome', 'name');
            $table->renameColumn('categoria', 'category');
            $table->renameColumn('nome_capitao', 'captain_name');
            $table->renameColumn('telefone', 'phone');
            $table->renameColumn('criado_em', 'created_at');
            $table->renameColumn('atualizado_em', 'updated_at');
        });

        Schema::rename('itens_agendamento', 'booking_items');
        Schema::rename('agendamentos', 'bookings');
        Schema::rename('produtos', 'products');
        Schema::rename('times', 'teams');
    }
};
