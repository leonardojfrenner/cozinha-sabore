@extends('layouts.app')

@section('title', 'Histórico de Pedidos - Cozinha Sabore')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Histórico de Pedidos</h1>
    <p class="text-gray-600 mt-2">Visualize todos os pedidos já processados</p>
</div>

<!-- Filtros -->
<div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
    <div class="flex flex-wrap gap-4">
        <div>
            <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status_filter" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">Todos</option>
                <option value="NOVO">Novo</option>
                <option value="CONCLUIDO">Concluído</option>
            </select>
        </div>
        <div>
            <label for="date_filter" class="block text-sm font-medium text-gray-700 mb-1">Data</label>
            <input type="date" id="date_filter" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
        </div>
        <div class="flex items-end">
            <button onclick="filterPedidos()" class="btn-primary px-4 py-2 rounded-md text-white text-sm font-medium">
                Filtrar
            </button>
        </div>
    </div>
</div>

@if($pedidos->count() > 0)
    <div class="grid gap-6" id="pedidos-container">
        @foreach($pedidos as $pedido)
            <div class="pedido-card bg-white rounded-lg shadow-md p-6 border-l-4 {{ $pedido['status'] === 'NOVO' ? 'border-blue-500' : 'border-green-500' }}" 
                 data-status="{{ $pedido['status'] }}" 
                 data-date="{{ \Carbon\Carbon::parse($pedido['criadoEm'])->format('Y-m-d') }}">
                
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

                <!-- Status do Pedido -->
                <div class="text-center">
                    @if($pedido['status'] === 'CONCLUIDO')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Pedido Concluído
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Pedido Pendente
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Estatísticas -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total de Pedidos</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $pedidos->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pedidos Concluídos</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $pedidos->where('status', 'CONCLUIDO')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Faturamento Total</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        R$ {{ number_format($pedidos->sum(function($pedido) { 
                            return collect($pedido['itens'])->sum(function($item) { 
                                return $item['itemRestaurante']['preco'] * $item['quantidade']; 
                            }); 
                        }), 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
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

@push('scripts')
<script>
function filterPedidos() {
    const statusFilter = document.getElementById('status_filter').value;
    const dateFilter = document.getElementById('date_filter').value;
    const pedidos = document.querySelectorAll('.pedido-card');
    
    pedidos.forEach(pedido => {
        const pedidoStatus = pedido.getAttribute('data-status');
        const pedidoDate = pedido.getAttribute('data-date');
        
        let showPedido = true;
        
        if (statusFilter && pedidoStatus !== statusFilter) {
            showPedido = false;
        }
        
        if (dateFilter && pedidoDate !== dateFilter) {
            showPedido = false;
        }
        
        pedido.style.display = showPedido ? 'block' : 'none';
    });
}

// Aplicar filtros automaticamente quando os campos mudarem
document.getElementById('status_filter').addEventListener('change', filterPedidos);
document.getElementById('date_filter').addEventListener('change', filterPedidos);
</script>
@endpush
