<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalvarTimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['nome' => ['required', 'string', 'max:120'], 'categoria' => ['required', 'string', 'max:60'], 'nome_capitao' => ['required', 'string', 'max:120'], 'telefone' => ['required', 'string', 'max:20', 'regex:/^[0-9+() .-]{8,20}$/', 'unique:times,telefone'.($this->route('time') ? ','.$this->route('time')->id : '')]];
    }

    public function messages(): array
    {
        return ['required' => 'O campo :attribute é obrigatório.', 'telefone.regex' => 'Informe um telefone válido.', 'telefone.unique' => 'Este telefone já está cadastrado.'];
    }
}
