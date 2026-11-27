<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalvarProdutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['nome' => 'required|string|max:120', 'descricao' => 'nullable|string', 'tipo' => 'required|in:servico,produto', 'categoria' => 'required|string|max:80', 'preco' => 'required|numeric|min:0.01', 'ativo' => 'sometimes|boolean', 'duracao_minutos' => 'nullable|integer|min:1|required_if:tipo,servico', 'estoque' => 'nullable|integer|min:0|required_if:tipo,produto'];
    }

    public function messages(): array
    {
        return ['required' => 'O campo :attribute é obrigatório.', 'preco.min' => 'O preço deve ser maior que zero.', 'required_if' => 'Este campo é necessário para o tipo selecionado.'];
    }
}
