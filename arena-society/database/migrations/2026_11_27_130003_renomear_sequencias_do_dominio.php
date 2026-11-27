<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $sequencias = [
        ['teams_id_seq', 'times_id_seq'],
        ['products_id_seq', 'produtos_id_seq'],
        ['bookings_id_seq', 'agendamentos_id_seq'],
        ['booking_items_id_seq', 'itens_agendamento_id_seq'],
    ];

    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->sequencias as [$anterior, $novo]) {
            if (DB::selectOne('SELECT to_regclass(?) AS nome', [$anterior])?->nome) {
                DB::statement('ALTER SEQUENCE "'.$anterior.'" RENAME TO "'.$novo.'"');
            }
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach (array_reverse($this->sequencias) as [$anterior, $novo]) {
            if (DB::selectOne('SELECT to_regclass(?) AS nome', [$novo])?->nome) {
                DB::statement('ALTER SEQUENCE "'.$novo.'" RENAME TO "'.$anterior.'"');
            }
        }
    }
};
