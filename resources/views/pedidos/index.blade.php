@extends('layouts.app')

@section('title', 'Pedidos - Cozinha Sabore')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Pedidos</h1>
    <p class="text-gray-600 mt-2">Gerencie os pedidos do seu restaurante</p>
</div>

@if($pedidos->count() > 0)
    <div class="grid gap-6">
        @foreach($pedidos as $pedido)
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 {{ $pedido['status'] === 'NOVO' ? 'border-blue-500' : 'border-green-500' }}">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Pedido #{{ $pedido['id'] }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($pedido['criadoEm'])->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $pedido['status'] === 'NOVO' ? 'status-novo' : 'status-concluido' }}">
                        {{ $pedido['status'] }}
                    </span>
                </div>

                <!-- Informações do Cliente -->
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-800 mb-2">Cliente</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <div><span class="font-medium">Nome:</span> {{ $pedido['cliente']['nome'] }}</div>
                        <div><span class="font-medium">Telefone:</span> {{ $pedido['cliente']['telefone'] }}</div>
                        <div><span class="font-medium">Email:</span> {{ $pedido['cliente']['email'] }}</div>
                        <div><span class="font-medium">CPF:</span> {{ $pedido['cliente']['cpf'] }}</div>
                    </div>
                    <div class="mt-2 text-sm">
                        <span class="font-medium">Endereço:</span> 
                        {{ $pedido['cliente']['rua'] }}, {{ $pedido['cliente']['numero'] }} - {{ $pedido['cliente']['bairro'] }}, {{ $pedido['cliente']['cidade'] }}/{{ $pedido['cliente']['estado'] }}
                    </div>
                </div>

                <!-- Itens do Pedido -->
                <div class="mb-4">
                    <h4 class="font-medium text-gray-800 mb-3">Itens do Pedido</h4>
                    <div class="space-y-3">
                        @foreach($pedido['itens'] as $item)
                            <div class="flex justify-between items-start p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <h5 class="font-medium text-gray-800">{{ $item['itemRestaurante']['nome'] }}</h5>
                                    <p class="text-sm text-gray-600 mt-1">{{ $item['itemRestaurante']['descricao'] }}</p>
                                    <div class="flex items-center mt-2 space-x-4 text-sm text-gray-600">
                                        <span>Qtd: {{ $item['quantidade'] }}</span>
                                        <span>R$ {{ number_format($item['itemRestaurante']['preco'], 2, ',', '.') }}</span>
                                        <span class="font-medium">Total: R$ {{ number_format($item['itemRestaurante']['preco'] * $item['quantidade'], 2, ',', '.') }}</span>
                                    </div>
                                    
                                    @if($item['ingredientesRemovidos'])
                                        <div class="mt-2 text-sm">
                                            <span class="font-medium text-red-600">Removido:</span> {{ $item['ingredientesRemovidos'] }}
                                        </div>
                                    @endif
                                    
                                    @if($item['ingredientesAdicionados'])
                                        <div class="mt-2 text-sm">
                                            <span class="font-medium text-green-600">Adicionado:</span> {{ $item['ingredientesAdicionados'] }}
                                        </div>
                                    @endif
                                    
                                    @if($item['observacoes'])
                                        <div class="mt-2 text-sm">
                                            <span class="font-medium text-blue-600">Observações:</span> {{ $item['observacoes'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($pedido['observacoesGerais'])
                    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="font-medium text-yellow-800 mb-1">Observações Gerais</h4>
                        <p class="text-sm text-yellow-700">{{ $pedido['observacoesGerais'] }}</p>
                    </div>
                @endif

                <!-- Total do Pedido -->
                <div class="mb-4 p-3 bg-gray-100 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-800">Total do Pedido:</span>
                        <span class="text-xl font-bold text-gray-800">
                            R$ {{ number_format(collect($pedido['itens'])->sum(function($item) { return $item['itemRestaurante']['preco'] * $item['quantidade']; }), 2, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Botões de Ação -->
                @if($pedido['status'] === 'NOVO')
                    <div class="flex justify-end space-x-3">
                        <form method="POST" action="{{ route('pedidos.update-status', $pedido['id']) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="CONCLUIDO">
                            <button type="submit" class="btn-primary px-6 py-2 rounded-md text-white font-medium hover:bg-green-600 transition-colors">
                                Marcar como Concluído
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-center">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Pedido Concluído
                        </span>
                    </div>
                @endif
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
