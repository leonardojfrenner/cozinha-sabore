@extends('layouts.app')

@section('title', 'Cardápio')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cardapio.css') }}">
@endpush

@section('content')
    @php
        $exibirControlesCardapio = false;
    @endphp

    <div class="cardapio-wrapper">
        <div class="cardapio-header">
            <div class="cardapio-header__text">
                <p class="cardapio-header__eyebrow">Gestão do cardápio</p>
                <h1 class="cardapio-header__title">Cardápio do restaurante</h1>
                <p class="cardapio-header__description">
                    Revise os pratos disponíveis, atualize valores e mantenha tudo sincronizado com a cozinha.
                </p>
            </div>
            @if ($exibirControlesCardapio)
                <div class="cardapio-header__actions">
                    <a href="{{ route('cardapio.create') }}" class="cardapio-create-button">
                        Cadastrar item
                    </a>
                </div>
            @endif
        </div>

        @if ($itens->isEmpty())
            <div class="cardapio-empty">
                <span class="cardapio-empty__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                    </svg>
                </span>
                <p class="cardapio-empty__title">Nenhum item cadastrado ainda</p>
                <p>Os pratos do cardápio aparecerão aqui assim que forem publicados pelo time responsável.</p>
            </div>
        @else
            @php
                $colecao = collect($itens);
                $categorias = $colecao->pluck('categoria')->filter()->unique()->values();
                $resolveData = function ($item) {
                    $candidatos = [
                        $item['updatedAt'] ?? null,
                        $item['updated_at'] ?? null,
                        $item['createdAt'] ?? null,
                        $item['created_at'] ?? null,
                    ];

                    foreach ($candidatos as $valor) {
                        if (!empty($valor)) {
                            try {
                                return \Carbon\Carbon::parse($valor);
                            } catch (\Throwable $e) {
                                continue;
                            }
                        }
                    }

                    return null;
                };

                $ultimaModificacao = $colecao
                    ->map($resolveData)
                    ->filter()
                    ->sortDesc()
                    ->first();
            @endphp

            <div class="cardapio-stats">
                <div class="cardapio-stat">
                    <p class="cardapio-stat__label">Itens cadastrados</p>
                    <p class="cardapio-stat__value">{{ $colecao->count() }}</p>
                </div>
                <div class="cardapio-stat">
                    <p class="cardapio-stat__label">Categorias únicas</p>
                    <p class="cardapio-stat__value">{{ $categorias->count() }}</p>
                </div>
            </div>

            @if ($categorias->isNotEmpty())
                <section class="cardapio-categories" aria-labelledby="cardapio-categorias">
                    <div>
                        <p id="cardapio-categorias" class="cardapio-categories__title">Categorias em destaque</p>
                <p class="cardapio-categories__subtitle">
                    Use estas tags como referência para manter o cardápio organizado.
                </p>
                    </div>
                    <div class="cardapio-tags">
                        @foreach ($categorias->take(10) as $categoria)
                            <span class="cardapio-tag">{{ $categoria }}</span>
                        @endforeach
                        @if ($categorias->count() > 10)
                            <span class="cardapio-tag">+{{ $categorias->count() - 10 }} outras</span>
                        @endif
                    </div>
                </section>
            @endif

            <div class="cardapio-mobile-list">
                @foreach ($itens as $item)
                    @php
                        $itemAtivo = (bool) ($item['ativo'] ?? true);
                    @endphp
                    <article class="cardapio-item-card cardapio-item-card--{{ $itemAtivo ? 'ativo' : 'pausado' }}">
                        <div class="cardapio-item-card__header">
                            <div class="cardapio-item-card__image {{ !empty($item['imagemUrlCompleta'] ?? $item['imagemUrl']) ? 'has-image' : '' }}">
                                @if (!empty($item['imagemUrlCompleta'] ?? $item['imagemUrl']))
                                    <img
                                        src="{{ $item['imagemUrlCompleta'] ?? $item['imagemUrl'] }}"
                                        alt="{{ $item['nome'] }}"
                                        width="80"
                                        height="80"
                                        loading="lazy"
                                        onload="this.parentNode.classList.add('has-image');"
                                        onerror="this.parentNode.classList.remove('has-image'); this.remove();"
                                    >
                                @endif
                                <span class="cardapio-image__fallback">sem imagem</span>
                            </div>
                            <div class="cardapio-item-card__content">
                                <div class="cardapio-item-card__header-text">
                                    <p class="cardapio-item-card__name">
                                        {{ $item['nome'] ?? 'Item sem nome' }}
                                    </p>
                                    <span class="cardapio-status {{ $itemAtivo ? 'cardapio-status--ativo' : 'cardapio-status--pausado' }}">
                                        {{ $itemAtivo ? 'Disponível' : 'Pausado' }}
                                    </span>
                                    @if (!empty($item['categoria']))
                                        <p class="cardapio-item-card__category">
                                            {{ $item['categoria'] }}
                                        </p>
                                    @endif
                                </div>
                                @if (!empty($item['descricao']))
                                    <p class="cardapio-item-card__description">
                                        {{ $item['descricao'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="cardapio-item-card__footer">
                            <div>
                                <span class="cardapio-item-card__price">
                                    R$ {{ number_format($item['preco'] ?? 0, 2, ',', '.') }}
                                </span>
                                <div>
                                    @php
                                        $dataItem = $resolveData($item);
                                    @endphp
                                    Atualizado em
                                    @if ($dataItem)
                                        {{ $dataItem->format('d/m/Y H:i') }}
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                            @if ($exibirControlesCardapio)
                                <div class="cardapio-item-card__actions">
                                    <form
                                        action="{{ route('cardapio.alterar-disponibilidade', $item['id']) }}"
                                        method="POST"
                                        data-confirm="Tem certeza que deseja {{ $itemAtivo ? 'pausar' : 'reativar' }} este item?"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="ativo" value="{{ $itemAtivo ? 0 : 1 }}">
                                        <button
                                            type="submit"
                                            class="cardapio-button {{ $itemAtivo ? 'cardapio-button--danger' : 'cardapio-button--primary' }}"
                                        >
                                            {{ $itemAtivo ? 'Pausar item' : 'Reativar item' }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="cardapio-table-wrapper" role="region" aria-label="Itens do cardápio">
                <table class="cardapio-table">
                    <thead>
                        <tr>
                            <th scope="col">Item</th>
                            <th scope="col">Categoria</th>
                            <th scope="col">Preço</th>
                            <th scope="col">Atualizado em</th>
                            @if ($exibirControlesCardapio)
                                <th scope="col" style="text-align:right;">Ações</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($itens as $item)
                            @php
                                $itemAtivo = (bool) ($item['ativo'] ?? true);
                            @endphp
                            <tr>
                                <td>
                                    <div class="cardapio-table__item">
                                <div class="cardapio-table__image {{ !empty($item['imagemUrlCompleta'] ?? $item['imagemUrl']) ? 'has-image' : '' }}">
                                    @if (!empty($item['imagemUrlCompleta'] ?? $item['imagemUrl']))
                                                <img
                                            src="{{ $item['imagemUrlCompleta'] ?? $item['imagemUrl'] }}"
                                                    alt="{{ $item['nome'] }}"
                                                    width="70"
                                                    height="70"
                                                    loading="lazy"
                                                    onload="this.parentNode.classList.add('has-image');"
                                                    onerror="this.parentNode.classList.remove('has-image'); this.remove();"
                                                >
                                            @endif
                                            <span class="cardapio-image__fallback">sem imagem</span>
                                        </div>
                                        <div>
                                            <div class="cardapio-table__name-wrapper">
                                                <p class="cardapio-table__name">
                                                    {{ $item['nome'] ?? 'Item sem nome' }}
                                                </p>
                                                <span class="cardapio-status {{ $itemAtivo ? 'cardapio-status--ativo' : 'cardapio-status--pausado' }}">
                                                    {{ $itemAtivo ? 'Disponível' : 'Pausado' }}
                                                </span>
                                            </div>
                                            @if (!empty($item['descricao']))
                                                <p class="cardapio-table__description">
                                                    {{ $item['descricao'] }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="cardapio-table__date">
                                    {{ $item['categoria'] ?? '-' }}
                                </td>
                                <td class="cardapio-table__price">
                                    R$ {{ number_format($item['preco'] ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="cardapio-table__date">
                                    @php
                                        $dataItem = $resolveData($item);
                                    @endphp
                                    @if ($dataItem)
                                        {{ $dataItem->format('d/m/Y H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                @if ($exibirControlesCardapio)
                                    <td style="text-align:right;">
                                        <div class="cardapio-table__actions">
                                            <form
                                                action="{{ route('cardapio.alterar-disponibilidade', $item['id']) }}"
                                                method="POST"
                                                data-confirm="Tem certeza que deseja {{ $itemAtivo ? 'pausar' : 'reativar' }} este item?"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="ativo" value="{{ $itemAtivo ? 0 : 1 }}">
                                                <button
                                                    type="submit"
                                                    class="cardapio-button {{ $itemAtivo ? 'cardapio-button--danger' : 'cardapio-button--primary' }}"
                                                >
                                                    {{ $itemAtivo ? 'Pausar item' : 'Reativar item' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection


