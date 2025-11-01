@extends('layouts.app')

@section('title', 'Pedidos - Cozinha Sabore')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Pedidos</h1>
            <p class="text-gray-600 mt-2">Gerencie os pedidos do seu restaurante</p>
        </div>
        <div class="text-sm text-gray-500">
            <span class="font-medium text-gray-700">{{ $pedidos->count() }}</span> pedido(s)
        </div>
    </div>
</div>

@if($pedidos->count() > 0)
    <div class="grid gap-6 md:gap-8">
        @foreach($pedidos as $pedido)
            @php
                $statusInfo = match(strtoupper($pedido['status'] ?? 'NOVO')) {
                    'NOVO' => [
                        'class' => 'bg-blue-50 border-blue-200',
                        'badge' => 'status-novo',
                        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        'border' => 'border-l-4 border-blue-500'
                    ],
                    'EM_PREPARO' => [
                        'class' => 'bg-amber-50 border-amber-200',
                        'badge' => 'status-em_preparo',
                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        'border' => 'border-l-4 border-amber-500'
                    ],
                    'CONCLUIDO' => [
                        'class' => 'bg-green-50 border-green-200',
                        'badge' => 'status-concluido',
                        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        'border' => 'border-l-4 border-green-500'
                    ],
                    'CANCELADO' => [
                        'class' => 'bg-red-50 border-red-200',
                        'badge' => 'status-cancelado',
                        'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                        'border' => 'border-l-4 border-red-500'
                    ],
                    default => [
                        'class' => 'bg-blue-50 border-blue-200',
                        'badge' => 'status-novo',
                        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        'border' => 'border-l-4 border-blue-500'
                    ]
                };
            @endphp
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 {{ $statusInfo['border'] }}">
                <!-- Header do Card -->
                <div class="bg-gradient-to-r {{ $statusInfo['class'] }} px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                @php
                                    $iconColor = match(strtoupper($pedido['status'] ?? 'NOVO')) {
                                        'NOVO' => 'text-blue-600',
                                        'EM_PREPARO' => 'text-amber-600',
                                        'CONCLUIDO' => 'text-green-600',
                                        'CANCELADO' => 'text-red-600',
                                        default => 'text-blue-600'
                                    };
                                @endphp
                                <svg class="w-6 h-6 {{ $iconColor }} opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusInfo['icon'] }}"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    Pedido #{{ $pedido['id'] ?? 'N/A' }}
                                </h3>
                                <p class="text-sm text-gray-600 mt-0.5 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @if(isset($pedido['criadoEm']))
                                        {{ \Carbon\Carbon::parse($pedido['criadoEm'])->format('d/m/Y') }} às {{ \Carbon\Carbon::parse($pedido['criadoEm'])->format('H:i') }}
                                    @elseif(isset($pedido['criado_em']))
                                        {{ \Carbon\Carbon::parse($pedido['criado_em'])->format('d/m/Y') }} às {{ \Carbon\Carbon::parse($pedido['criado_em'])->format('H:i') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <span class="px-4 py-2 rounded-full text-sm font-semibold shadow-sm {{ $statusInfo['badge'] }}">
                            {{ $pedido['status'] ?? 'NOVO' }}
                        </span>
                    </div>
                </div>

                <!-- Corpo do Card -->
                <div class="p-6 space-y-4">

                    <!-- Informações do Cliente -->
                    @if(isset($pedido['cliente']))
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center mb-3">
                            <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <h4 class="font-semibold text-gray-900">Dados do Cliente</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div class="flex items-start">
                                <span class="font-medium text-gray-700 min-w-[80px]">Nome:</span>
                                <span class="text-gray-900">{{ $pedido['cliente']['nome'] ?? '-' }}</span>
                            </div>
                            <div class="flex items-start">
                                <span class="font-medium text-gray-700 min-w-[80px]">Telefone:</span>
                                <span class="text-gray-900">{{ $pedido['cliente']['telefone'] ?? '-' }}</span>
                            </div>
                            <div class="flex items-start">
                                <span class="font-medium text-gray-700 min-w-[80px]">Email:</span>
                                <span class="text-gray-900 break-all">{{ $pedido['cliente']['email'] ?? '-' }}</span>
                            </div>
                            <div class="flex items-start">
                                <span class="font-medium text-gray-700 min-w-[80px]">CPF:</span>
                                <span class="text-gray-900">{{ $pedido['cliente']['cpf'] ?? '-' }}</span>
                            </div>
                        </div>
                        @if(isset($pedido['cliente']['rua']) || isset($pedido['cliente']['endereco']))
                        <div class="mt-3 pt-3 border-t border-gray-300 flex items-start">
                            <svg class="w-5 h-5 text-gray-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <span class="font-medium text-gray-700 text-sm">Endereço:</span>
                                <p class="text-gray-900 text-sm mt-1">
                                    @if(isset($pedido['cliente']['rua']))
                                        {{ $pedido['cliente']['rua'] }}, {{ $pedido['cliente']['numero'] ?? '' }} - {{ $pedido['cliente']['bairro'] ?? '' }}, {{ $pedido['cliente']['cidade'] ?? '' }}/{{ $pedido['cliente']['estado'] ?? '' }}
                                    @else
                                        {{ $pedido['cliente']['endereco'] ?? '-' }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Itens do Pedido -->
                    @if(isset($pedido['itens']) && count($pedido['itens']) > 0)
                    <div>
                        <div class="flex items-center mb-4">
                            <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h4 class="font-semibold text-gray-900">Itens do Pedido</h4>
                            <span class="ml-2 px-2 py-0.5 bg-gray-200 text-gray-700 rounded-full text-xs font-medium">{{ count($pedido['itens']) }}</span>
                        </div>
                        <div class="space-y-3">
                            @foreach($pedido['itens'] as $item)
                                @php
                                    $itemNome = $item['itemRestaurante']['nome'] ?? $item['nome'] ?? 'Item';
                                    $itemDesc = $item['itemRestaurante']['descricao'] ?? $item['descricao'] ?? '';
                                    $itemPreco = $item['itemRestaurante']['preco'] ?? $item['preco'] ?? 0;
                                    $quantidade = $item['quantidade'] ?? 1;
                                    $totalItem = $itemPreco * $quantidade;
                                @endphp
                                <div class="bg-gradient-to-r from-white to-gray-50 rounded-lg p-4 border border-gray-200 hover:border-gray-300 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <h5 class="font-semibold text-gray-900">{{ $itemNome }}</h5>
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">x{{ $quantidade }}</span>
                                            </div>
                                            @if($itemDesc)
                                            <p class="text-sm text-gray-600 mt-1 mb-2">{{ $itemDesc }}</p>
                                            @endif
                                            
                                            <div class="flex items-center space-x-4 mt-2">
                                                <span class="text-sm text-gray-600">Unit: <span class="font-medium">R$ {{ number_format($itemPreco, 2, ',', '.') }}</span></span>
                                                <span class="text-base font-bold text-gray-900">Total: R$ {{ number_format($totalItem, 2, ',', '.') }}</span>
                                            </div>
                                            
                                            <div class="mt-3 space-y-1">
                                                @if(isset($item['ingredientesRemovidos']) && $item['ingredientesRemovidos'])
                                                    <div class="flex items-center text-xs">
                                                        <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        <span class="font-medium text-red-700">Removido:</span>
                                                        <span class="text-red-600 ml-1">{{ $item['ingredientesRemovidos'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                @if(isset($item['ingredientesAdicionados']) && $item['ingredientesAdicionados'])
                                                    <div class="flex items-center text-xs">
                                                        <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                        </svg>
                                                        <span class="font-medium text-green-700">Adicionado:</span>
                                                        <span class="text-green-600 ml-1">{{ $item['ingredientesAdicionados'] }}</span>
                                                    </div>
                                                @endif
                                                
                                                @if(isset($item['observacoes']) && $item['observacoes'])
                                                    <div class="flex items-start text-xs mt-2 pt-2 border-t border-gray-200">
                                                        <svg class="w-4 h-4 text-blue-500 mr-1 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                        </svg>
                                                        <div>
                                                            <span class="font-medium text-blue-700">Observações:</span>
                                                            <span class="text-blue-600 ml-1">{{ $item['observacoes'] }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(isset($pedido['observacoesGerais']) && $pedido['observacoesGerais'])
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-yellow-900 mb-1">Observações Gerais</h4>
                                    <p class="text-sm text-yellow-800">{{ $pedido['observacoesGerais'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Total do Pedido -->
                    @if(isset($pedido['itens']) && count($pedido['itens']) > 0)
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg p-4 border-2 border-amber-200 mb-4">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-lg font-semibold text-gray-900">Total do Pedido</span>
                            </div>
                            <span class="text-2xl font-bold text-amber-700">
                                @php
                                    $total = 0;
                                    foreach($pedido['itens'] as $item) {
                                        $preco = $item['itemRestaurante']['preco'] ?? $item['preco'] ?? 0;
                                        $qtd = $item['quantidade'] ?? 1;
                                        $total += $preco * $qtd;
                                    }
                                @endphp
                                R$ {{ number_format($total, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @endif

                    <!-- Botões de Ação -->
                    @php
                        $statusAtual = strtoupper($pedido['status'] ?? 'NOVO');
                        $pedidoId = $pedido['id'] ?? null;
                    @endphp
                    
                    @if($pedidoId)
                    <div class="mt-6 pt-6 border-t-2 border-gray-200">
                        @if($statusAtual === 'NOVO')
                            <div class="space-y-4">
                                <div class="text-center py-3 px-4 bg-blue-100 rounded-lg border-2 border-blue-400">
                                    <span class="text-lg font-extrabold text-blue-900">⚡ Novo Pedido - Aguardando Ação</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <form method="POST" action="{{ route('pedidos.update-status', $pedidoId) }}" class="update-status-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="EM_PREPARO">
                                        <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-4 bg-amber-500 hover:bg-amber-600 text-white font-bold text-lg rounded-xl shadow-xl hover:shadow-2xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none border-2 border-amber-700">
                                            <svg class="w-7 h-7 mr-3 hidden loading-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            <svg class="w-7 h-7 mr-3 icon-normal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="button-text">🔥 INICIAR PREPARO</span>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('pedidos.update-status', $pedidoId) }}" class="update-status-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="CANCELADO">
                                        <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-4 bg-red-600 hover:bg-red-700 text-white font-bold text-lg rounded-xl shadow-xl hover:shadow-2xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none border-2 border-red-800" onclick="return confirm('⚠️ Tem certeza que deseja cancelar este pedido?')">
                                            <svg class="w-7 h-7 mr-3 hidden loading-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            <svg class="w-7 h-7 mr-3 icon-normal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="button-text">❌ CANCELAR</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @elseif($statusAtual === 'EM_PREPARO')
                            <div class="space-y-4">
                                <div class="flex items-center justify-center py-3 px-4 bg-amber-100 rounded-lg border-2 border-amber-500">
                                    <svg class="w-6 h-6 mr-2 text-amber-700 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-lg font-extrabold text-amber-900">⏳ Pedido em Preparo...</span>
                                </div>
                                <form method="POST" action="{{ route('pedidos.update-status', $pedidoId) }}" class="update-status-form w-full">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="CONCLUIDO">
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-8 py-5 bg-green-600 hover:bg-green-700 text-white font-bold text-xl rounded-xl shadow-xl hover:shadow-2xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none border-2 border-green-800">
                                        <svg class="w-8 h-8 mr-3 hidden loading-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        <svg class="w-8 h-8 mr-3 icon-normal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="button-text">✅ MARCAR COMO CONCLUÍDO</span>
                                    </button>
                                </form>
                            </div>
                        @elseif($statusAtual === 'CONCLUIDO')
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <div class="flex items-center px-6 py-3 rounded-xl bg-green-100 border-2 border-green-500 shadow-md">
                                    <svg class="w-7 h-7 mr-3 text-green-700" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-base font-extrabold text-green-900">✅ Pedido Concluído!</span>
                                </div>
                                <form method="POST" action="{{ route('pedidos.update-status', $pedidoId) }}" class="inline update-status-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="CONCLUIDO">
                                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none border-2 border-green-800">
                                        <svg class="w-5 h-5 mr-2 hidden loading-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        <svg class="w-5 h-5 mr-2 icon-normal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="button-text">Reconfirmar</span>
                                    </button>
                                </form>
                            </div>
                        @elseif($statusAtual === 'CANCELADO')
                            <div class="flex justify-center py-2">
                                <div class="inline-flex items-center px-8 py-4 rounded-xl bg-red-100 border-2 border-red-500 shadow-md">
                                    <svg class="w-7 h-7 mr-3 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-base font-extrabold text-red-900">❌ Pedido Cancelado</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    @else
                    <div class="pt-4 border-t border-gray-200 text-center text-red-600 font-semibold text-sm">
                        ⚠️ ID do pedido não encontrado
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum pedido encontrado</h3>
        <p class="mt-1 text-sm text-gray-500">Quando houver pedidos, eles aparecerão aqui.</p>
    </div>
@endif
@endsection
