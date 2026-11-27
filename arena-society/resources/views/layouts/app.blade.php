<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Arena Society')</title>
    <style>
        body { font: 16px Arial; background: #f2f5f2; color: #18251d; margin: 0; }
        header { background: #123d29; color: white; padding: 22px 6%; display: flex; justify-content: space-between; align-items: center; }
        main { max-width: 1100px; margin: 28px auto; padding: 0 18px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 18px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 3px 15px #14251a12; margin-bottom: 18px; }
        h2 { color: #17643d; }
        input, select, button { padding: 10px; border: 1px solid #ccd5ce; border-radius: 6px; margin: 4px; }
        button { background: #17643d; color: white; border: 0; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        td, th { text-align: left; border-bottom: 1px solid #e4e9e5; padding: 9px; }
        .success { background: #dbf3e1; padding: 12px; }
        .error { background: #ffe1df; color: #8b1d16; padding: 12px; }
        small { color: #59645d; }
    </style>
</head>
<body>
    <header>
        <strong>⚽ Arena Society</strong>
        <span>Gestão de quadras e reservas</span>
        @auth
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Sair</button>
            </form>
        @endauth
    </header>

    <main>
        @if (session('success'))
            <p class="success">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <p class="error">{{ $errors->first() }}</p>
        @endif

        @yield('content')
    </main>
</body>
</html>
