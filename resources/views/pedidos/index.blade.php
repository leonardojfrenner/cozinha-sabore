@extends('layouts.app')

@section('title', 'Pedidos - Cozinha Sabore')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pedidos.css') }}">
@endpush

@section('content')
<div class="orders-header">
    <div class="orders-header__left">
        <h1>Pedidos</h1>
        <p class="orders-header__subtitle">Gerencie os pedidos do seu restaurante</p>
    </div>
    <div class="orders-header__count">
        <span>{{ $pedidos->count() }}</span> pedido(s)
    </div>
</div>

@if($pedidos->count() > 0)
    <div class="orders-grid">
        @foreach($pedidos as $pedido)
            @php
                $statusInfo = match(strtoupper($pedido['status'] ?? 'NOVO')) {
                    'NOVO' => [
                        'card_class' => 'order-card order-card--novo',
                        'badge_class' => 'status-badge status-badge--novo',
                        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    ],
                    'EM_PREPARO' => [
                        'card_class' => 'order-card order-card--em-preparo',
                        'badge_class' => 'status-badge status-badge--em-preparo',
                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    'CONCLUIDO' => [
                        'card_class' => 'order-card order-card--concluido',
                        'badge_class' => 'status-badge status-badge--concluido',
                        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    'CANCELADO' => [
                        'card_class' => 'order-card order-card--cancelado',
                        'badge_class' => 'status-badge status-badge--cancelado',
                        'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    default => [
                        'card_class' => 'order-card order-card--novo',
                        'badge_class' => 'status-badge status-badge--novo',
                        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    ]
                };
            @endphp
            <div class="{{ $statusInfo['card_class'] }}">
                <div class="order-card__header">
                    <div class="order-card__header-meta">
                        <svg class="status-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusInfo['icon'] }}"></path>
                        </svg>
                        <div>
                            <h3 class="order-card__title">
                                Pedido #{{ $pedido['id'] ?? 'N/A' }}
                            </h3>
                            <p class="order-card__meta">
                                <svg class="order-card__meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <span class="{{ $statusInfo['badge_class'] }}">
                        {{ $pedido['status'] ?? 'NOVO' }}
                    </span>
                </div>

                <div class="order-card__body">
                    <div class="order-section order-section--compact">
                        <div class="order-section__header">
                            <svg class="order-section__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <h4 class="order-section__title">Cliente</h4>
                        </div>
                        <div class="order-section__row order-section__row--single">
                            <span class="order-section__label">Nome:</span>
                            <span class="order-section__value">{{ $pedido['cliente']['nome'] ?? 'Cliente' }}</span>
                        </div>
                    </div>

                    @if(isset($pedido['itens']) && count($pedido['itens']) > 0)
                    <div class="order-section">
                        <div class="order-section__header">
                            <svg class="order-section__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h4 class="order-section__title">Itens do Pedido</h4>
                            <span class="order-item__quantity">x{{ count($pedido['itens']) }}</span>
                        </div>
                        <div class="order-items__wrapper">
                            @foreach($pedido['itens'] as $item)
                                @php
                                    $itemNome = $item['itemRestaurante']['nome'] ?? $item['nome'] ?? 'Item';
                                    $itemDesc = $item['itemRestaurante']['descricao'] ?? $item['descricao'] ?? '';
                                    $itemPreco = $item['itemRestaurante']['preco'] ?? $item['preco'] ?? 0;
                                    $quantidade = $item['quantidade'] ?? 1;
                                    $totalItem = $itemPreco * $quantidade;
                                @endphp
                                <div class="order-item">
                                    <div class="order-item__header">
                                        <h5 class="order-item__title">{{ $itemNome }}</h5>
                                        <span class="order-item__quantity">x{{ $quantidade }}</span>
                                    </div>
                                    @if($itemDesc)
                                    <p class="order-item__description">{{ $itemDesc }}</p>
                                    @endif

                                    <div class="order-item__pricing">
                                        <span>Unit: <strong>R$ {{ number_format($itemPreco, 2, ',', '.') }}</strong></span>
                                        <span>Total: <strong>R$ {{ number_format($totalItem, 2, ',', '.') }}</strong></span>
                                    </div>

                                    <div class="order-item__notes">
                                        @if(isset($item['ingredientesRemovidos']) && $item['ingredientesRemovidos'])
                                            <div class="order-item__flag order-item__flag--removed">
                                                <svg class="order-flag__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                <span><strong>Removido:</strong> {{ $item['ingredientesRemovidos'] }}</span>
                                            </div>
                                        @endif

                                        @if(isset($item['ingredientesAdicionados']) && $item['ingredientesAdicionados'])
                                            <div class="order-item__flag order-item__flag--added">
                                                <svg class="order-flag__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                <span><strong>Adicionado:</strong> {{ $item['ingredientesAdicionados'] }}</span>
                                            </div>
                                        @endif

                                        @if(isset($item['observacoes']) && $item['observacoes'])
                                            <div class="order-item__flag order-item__flag--note">
                                                <svg class="order-flag__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                </svg>
                                                <span><strong>Observações:</strong> {{ $item['observacoes'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(isset($pedido['observacoesGerais']) && $pedido['observacoesGerais'])
                        <div class="order-observations">
                            <h4>Observações Gerais</h4>
                            <p>{{ $pedido['observacoesGerais'] }}</p>
                        </div>
                    @endif

                    @if(isset($pedido['itens']) && count($pedido['itens']) > 0)
                    <div class="order-total">
                        <div class="order-total__label">
                            <svg class="order-total__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Total do Pedido
                        </div>
                        <span class="order-total__value">
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
                    @endif

                    @php
                        $statusAtual = strtoupper($pedido['status'] ?? 'NOVO');
                        $pedidoId = $pedido['id'] ?? null;
                    @endphp
                    
                    @if($pedidoId)
                    <div class="order-actions">
                        @if($statusAtual === 'NOVO')
                            <div class="order-alert order-alert--new">
                                ⚡ Novo Pedido - Aguardando Ação
                            </div>
                            <div class="order-actions__grid">
                                <form
                                    method="POST"
                                    action="{{ route('pedidos.update-status', $pedidoId) }}"
                                    class="update-status-form"
                                    data-confirm="⚠️ Tem certeza que deseja cancelar este pedido?"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="EM_PREPARO">
                                    <button type="submit" class="order-button order-button--start">
                                        <svg class="loading-spinner hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        <svg class="icon-normal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="button-text">🔥 INICIAR PREPARO</span>
                                    </button>
                                </form>
                                <form
                                    method="POST"
                                    action="{{ route('pedidos.update-status', $pedidoId) }}"
                                    class="update-status-form"
                                    data-confirm="⚠️ Tem certeza que deseja cancelar este pedido?"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="CANCELADO">
                                    <button type="submit" class="order-button order-button--cancel">
                                        <svg class="loading-spinner hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        <svg class="icon-normal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="button-text">❌ CANCELAR</span>
                                    </button>
                                </form>
                            </div>
                        @elseif($statusAtual === 'EM_PREPARO')
                            <div class="order-alert order-alert--preparing">
                                ⏳ Pedido em Preparo...
                            </div>
                                <form method="POST" action="{{ route('pedidos.update-status', $pedidoId) }}" class="update-status-form">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="CONCLUIDO">
                                <button type="submit" class="order-button order-button--finish">
                                    <svg class="loading-spinner hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <svg class="icon-normal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="button-text">✅ MARCAR COMO CONCLUÍDO</span>
                                </button>
                            </form>
                        @elseif($statusAtual === 'CONCLUIDO')
                            <div class="order-alert order-alert--success">
                                ✅ Pedido Concluído!
                            </div>
                        @elseif($statusAtual === 'CANCELADO')
                            <div class="order-alert order-alert--cancelled">
                                ❌ Pedido Cancelado
                            </div>
                        @endif
                    </div>
                    @else
                    <div class="order-alert order-alert--cancelled">
                        ⚠️ ID do pedido não encontrado
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
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

