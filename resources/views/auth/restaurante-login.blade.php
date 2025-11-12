<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Cozinha Sabore</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/auth-login.css') }}">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-header">
            <h1 class="login-title">Cozinha Sabore</h1>
            <p class="login-subtitle">Sistema de Gerenciamento de Pedidos</p>
        </div>

        @if ($errors->any())
            <div class="error-alert" role="alert">
                <strong>Ops, algo deu errado:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('restaurante.login') }}" class="login-form">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">
                    Email
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-input"
                    placeholder="Digite seu email"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    Senha
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    placeholder="Digite sua senha"
                    required
                >
            </div>

            <div class="form-checkbox-row">
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    value="1"
                    class="form-checkbox"
                    {{ old('remember') ? 'checked' : '' }}
                >
                <label for="remember">Lembrar de mim</label>
            </div>

            <button
                type="submit"
                class="form-submit"
            >
                Entrar
            </button>
        </form>

        <p class="info-text">
                Acesso restrito aos restaurantes cadastrados
        </p>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('.login-form');
            const rememberCheckbox = document.getElementById('remember');

            if (rememberCheckbox) {
                console.info('[Login] "Lembrar de mim" (carga inicial):', rememberCheckbox.checked);

                rememberCheckbox.addEventListener('change', () => {
                    console.info('[Login] "Lembrar de mim" alterado para:', rememberCheckbox.checked);
                });
            }

            if (form && rememberCheckbox) {
                form.addEventListener('submit', () => {
                    console.info('[Login] Enviando com "Lembrar de mim":', rememberCheckbox.checked);
                });
            }
        });
    </script>
</body>
</html>
