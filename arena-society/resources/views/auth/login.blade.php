@extends('layouts.app')
@section('title','Entrar | Arena Society')
@section('content')
<section class="card" style="max-width:440px;margin:60px auto"><h1>Acesso administrativo</h1><p>Entre com a conta configurada para esta instalação.</p><form method="post" action="{{ route('login.store') }}">@csrf<label for="email">E-mail</label><br><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required><br><label for="password">Senha</label><br><input id="password" name="password" type="password" autocomplete="current-password" required><br><button>Entrar</button></form></section>
@endsection
