<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cozinha Sabore')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .navbar {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }
        .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #d97706, #b45309);
        }
        .status-novo {
            background-color: #3b82f6;
            color: white;
        }
        .status-concluido {
            background-color: #10b981;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50">
    <nav class="navbar shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-white text-xl font-bold">Cozinha Sabore</h1>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    @if(session('restaurante_logado'))
                        <span class="text-white">Bem-vindo, {{ session('restaurante_logado')['nome'] }}</span>
                        <a href="{{ route('pedidos.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">
                            Pedidos
                        </a>
                        <a href="{{ route('pedidos.historico') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">
                            Histórico
                        </a>
                        <form method="POST" action="{{ route('restaurante.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">
                                Sair
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div-shadow>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>
