@extends('layouts.app')

@section('title', 'Histórico de Pedidos - Cozinha Sabore')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pedidos.css') }}">
@endpush

@section('content')
<div class="orders-header">
    <div class="orders-header__left">
        <h1>Histórico de Pedidos</h1>
        <p class="orders-header__subtitle">Visualize todos os pedidos já processados</p>
    </div>
</div>

<section class="cardapio-categories" aria-label="Filtros de histórico">
    <div>
        <p class="cardapio-categories__title">Filtros</p>
        <p class="cardapio-categories__subtitle">Selecione status e data para refinar os resultados.</p>
    </div>
    <div class="order-actions__grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="cardapio-form-field">
            <label for="status_filter">Status</label>
            <select id="status_filter" class="cardapio-form-input">
                <option value="">Todos</option>
                <option value="NOVO">Novo</option>
                <option value="CONCLUIDO">Concluído</option>
            </select>
        </div>
        <div class="cardapio-form-field">
            <label for="date_filter">Data</label>
            <input type="date" id="date_filter" class="cardapio-form-input">
        </div>
        <button type="button" class="order-button order-button--start" onclick="filterPedidos()">Filtrar</button>
    </div>
</section>

@if($pedidos->count() > 0)
    <div class="orders-grid" id="pedidos-container" style="margin-top: 24px;">
        @foreach($pedidos as $pedido)
            @php
                $statusInfo = match(strtoupper($pedido['status'] ?? 'NOVO')) {
                    'CONCLUIDO' => ['card_class' => 'order-card order-card--concluido', 'badge_class' => 'status-badge status-badge--concluido'],
                    'NOVO' => ['card_class' => 'order-card order-card--novo', 'badge_class' => 'status-badge status-badge--novo'],
                    default => ['card_class' => 'order-card order-card--em-preparo', 'badge_class' => 'status-badge status-badge--em-preparo'],
                };
                $dataPedido = !empty($pedido['criadoEm']) ? \Carbon\Carbon::parse($pedido['criadoEm']) : null;
            @endphp
            <article class="{{ $statusInfo['card_class'] }} pedido-card"
                     data-status="{{ $pedido['status'] }}"
                     data-date="{{ $dataPedido ? $dataPedido->format('Y-m-d') : '' }}">
                <header class="order-card__header">
                    <div class="order-card__header-meta">
                        <svg class="status-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="order-card__title">Pedido #{{ $pedido['id'] }}</h3>
                            @if ($dataPedido)
                                <p class="order-card__meta">
                                    {{ $dataPedido->format('d/m/Y H:i') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <span class="{{ $statusInfo['badge_class'] }}">{{ $pedido['status'] }}</span>
                </header>

                <div class="order-card__body">
                    <section class="order-section">
                        <div class="order-section__header">
                            <svg class="order-section__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <h4 class="order-section__title">Cliente</h4>
                        </div>
                        <div class="order-section__grid">
                            <div class="order-section__row">
                                <span class="order-section__label">Nome:</span>
                                <span class="order-section__value">{{ $pedido['cliente']['nome'] }}</span>
                            </div>
                            <div class="order-section__row">
                                <span class="order-section__label">Telefone:</span>
                                <span class="order-section__value">{{ $pedido['cliente']['telefone'] }}</span>
                            </div>
                            <div class="order-section__row">
                                <span class="order-section__label">Email:</span>
                                <span class="order-section__value">{{ $pedido['cliente']['email'] }}</span>
                            </div>
                            <div class="order-section__row">
                                <span class="order-section__label">CPF:</span>
                                <span class="order-section__value">{{ $pedido['cliente']['cpf'] }}</span>
                            </div>
                        </div>
                        <div class="order-address">
                            <svg class="order-section__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="order-section__value">
                                {{ $pedido['cliente']['rua'] }}, {{ $pedido['cliente']['numero'] }} - {{ $pedido['cliente']['bairro'] }},
                                {{ $pedido['cliente']['cidade'] }}/{{ $pedido['cliente']['estado'] }}
                            </span>
                        </div>
                    </section>

                    <section class="order-section">
                        <div class="order-section__header">
                            <svg class="order-section__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h4 class="order-section__title">Itens do Pedido</h4>
                        </div>
                        <div class="order-items__wrapper">
                            @foreach($pedido['itens'] as $item)
                                <div class="order-item">
                                    <div class="order-item__header">
                                        <h5 class="order-item__title">{{ $item['itemRestaurante']['nome'] }}</h5>
                                        <span class="order-item__quantity">x{{ $item['quantidade'] }}</span>
                                    </div>
                                    @if($item['itemRestaurante']['descricao'])
                                        <p class="order-item__description">{{ $item['itemRestaurante']['descricao'] }}</p>
                                    @endif
                                    <div class="order-item__pricing">
                                        <span>Unit: <strong>R$ {{ number_format($item['itemRestaurante']['preco'], 2, ',', '.') }}</strong></span>
                                        <span>Total: <strong>R$ {{ number_format($item['itemRestaurante']['preco'] * $item['quantidade'], 2, ',', '.') }}</strong></span>
                                    </div>
                                    <div class="order-item__notes">
                                        @if($item['ingredientesRemovidos'])
                                            <span class="order-item__flag order-item__flag--removed"><strong>Removido:</strong> {{ $item['ingredientesRemovidos'] }}</span>
                                        @endif
                                        @if($item['ingredientesAdicionados'])
                                            <span class="order-item__flag order-item__flag--added"><strong>Adicionado:</strong> {{ $item['ingredientesAdicionados'] }}</span>
                                        @endif
                                        @if($item['observacoes'])
                                            <span class="order-item__flag order-item__flag--note"><strong>Observações:</strong> {{ $item['observacoes'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    @if($pedido['observacoesGerais'])
                        <div class="order-observations">
                            <h4>Observações Gerais</h4>
                            <p>{{ $pedido['observacoesGerais'] }}</p>
                        </div>
                    @endif

                    <div class="order-total">
                        <div class="order-total__label">
                            <svg class="order-total__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Total do Pedido
                        </div>
                        <span class="order-total__value">
                            R$ {{ number_format(collect($pedido['itens'])->sum(fn($item) => $item['itemRestaurante']['preco'] * $item['quantidade']), 2, ',', '.') }}
                        </span>
                    </div>

                    <div class="order-alert {{ $pedido['status'] === 'CONCLUIDO' ? 'order-alert--success' : 'order-alert--new' }}">
                        @if($pedido['status'] === 'CONCLUIDO')
                            ✅ Pedido Concluído
                        @else
                            ⏳ Pedido Pendente
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <section class="cardapio-stats" style="margin-top: 32px;">
        <div class="cardapio-stat">
            <p class="cardapio-stat__label">Total de Pedidos</p>
            <p class="cardapio-stat__value">{{ $pedidos->count() }}</p>
        </div>
        <div class="cardapio-stat">
            <p class="cardapio-stat__label">Pedidos Concluídos</p>
            <p class="cardapio-stat__value">{{ $pedidos->where('status', 'CONCLUIDO')->count() }}</p>
        </div>
        <div class="cardapio-stat">
            <p class="cardapio-stat__label">Faturamento Total</p>
            <p class="cardapio-stat__value">
                R$ {{ number_format($pedidos->sum(function($pedido) {
                    return collect($pedido['itens'])->sum(function($item) {
                        return $item['itemRestaurante']['preco'] * $item['quantidade'];
                    });
                }), 2, ',', '.') }}
            </p>
        </div>
    </section>
@else
    <div class="order-empty">
        <svg class="order-empty__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3>Nenhum pedido encontrado</h3>
        <p>Quando houver pedidos, eles aparecerão aqui.</p>
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

document.getElementById('status_filter').addEventListener('change', filterPedidos);
document.getElementById('date_filter').addEventListener('change', filterPedidos);
</script>
@endpush
