<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalvarAgendamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['time_id' => 'required|exists:times,id', 'quadra' => 'required|string|max:60', 'inicio_em' => 'required|date|after_or_equal:now', 'duracao_horas' => 'required|integer|in:1,2', 'itens' => 'nullable|array', 'itens.*.produto_id' => 'required|integer|exists:produtos,id', 'itens.*.quantidade' => 'required|integer|min:1|max:100', 'observacoes' => 'nullable|string|max:1000'];
    }

    public function messages(): array
    {
        return ['after_or_equal' => 'Não é possível reservar em data passada.', 'duracao_horas.in' => 'A reserva deve durar 1 ou 2 horas.'];
    }
}
