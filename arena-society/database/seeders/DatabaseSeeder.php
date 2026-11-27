<?php

namespace Database\Seeders;

use App\Models\Produto;
use App\Models\Time;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $senha = config('app.senha_admin_semeadura');

        if (! is_string($senha) || $senha === '') {
            throw new \RuntimeException('Defina SENHA_ADMIN_SEMEADURA no arquivo .env antes de executar os seeders.');
        }

        User::updateOrCreate(['email' => 'admin@arenasociety'], ['name' => 'Administrador Arena', 'password' => Hash::make($senha)]);
        Time::firstOrCreate(['telefone' => '11999990000'], ['nome' => 'Society da Vila', 'categoria' => 'Adulto', 'nome_capitao' => 'Capitão Exemplo']);
        foreach ([['Locação de quadra (1h)', 'servico', 'Quadra', '120.00', 60], ['Arbitragem', 'servico', 'Serviço', '80.00', null], ['Aluguel de coletes', 'produto', 'Equipamento', '20.00', null], ['Bola society', 'produto', 'Equipamento', '15.00', null], ['Bebidas', 'produto', 'Bebidas', '35.00', null], ['Kit churrasqueira', 'produto', 'Churrasqueira', '90.00', null]] as [$nome, $tipo, $categoria, $preco, $duracao]) {
            Produto::firstOrCreate(['nome' => $nome], ['descricao' => 'Disponível para adicionar à reserva.', 'tipo' => $tipo, 'categoria' => $categoria, 'preco' => $preco, 'ativo' => true, 'duracao_minutos' => $duracao, 'estoque' => $tipo === 'produto' ? 30 : null]);
        }
    }
}
