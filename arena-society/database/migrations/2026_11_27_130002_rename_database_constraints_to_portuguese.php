<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $restricoes = [
        ['times', 'teams_pkey', 'times_pkey'],
        ['times', 'teams_phone_unique', 'times_telefone_unique'],
        ['produtos', 'products_pkey', 'produtos_pkey'],
        ['agendamentos', 'bookings_pkey', 'agendamentos_pkey'],
        ['agendamentos', 'bookings_team_id_foreign', 'agendamentos_time_id_foreign'],
        ['itens_agendamento', 'booking_items_pkey', 'itens_agendamento_pkey'],
        ['itens_agendamento', 'booking_items_booking_id_foreign', 'itens_agendamento_agendamento_id_foreign'],
        ['itens_agendamento', 'booking_items_product_id_foreign', 'itens_agendamento_produto_id_foreign'],
        ['itens_agendamento', 'booking_items_booking_id_product_id_unique', 'itens_agendamento_agendamento_id_produto_id_unique'],
    ];

    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->restricoes as [$tabela, $anterior, $novo]) {
            $existe = DB::selectOne('SELECT 1 FROM pg_constraint WHERE conrelid = to_regclass(?) AND conname = ?', [$tabela, $anterior]);
            if ($existe) {
                DB::statement('ALTER TABLE "'.$tabela.'" RENAME CONSTRAINT "'.$anterior.'" TO "'.$novo.'"');
            }
        }

        $indice = DB::selectOne("SELECT to_regclass('bookings_court_starts_at_status_index') AS nome");
        if ($indice?->nome) {
            DB::statement('ALTER INDEX bookings_court_starts_at_status_index RENAME TO agendamentos_quadra_inicio_em_status_index');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $indice = DB::selectOne("SELECT to_regclass('agendamentos_quadra_inicio_em_status_index') AS nome");
        if ($indice?->nome) {
            DB::statement('ALTER INDEX agendamentos_quadra_inicio_em_status_index RENAME TO bookings_court_starts_at_status_index');
        }

        foreach (array_reverse($this->restricoes) as [$tabela, $anterior, $novo]) {
            $existe = DB::selectOne('SELECT 1 FROM pg_constraint WHERE conrelid = to_regclass(?) AND conname = ?', [$tabela, $novo]);
            if ($existe) {
                DB::statement('ALTER TABLE "'.$tabela.'" RENAME CONSTRAINT "'.$novo.'" TO "'.$anterior.'"');
            }
        }
    }
};
