@extends('layouts.app')

@section('title', 'Editar item do cardápio')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cardapio-form.css') }}">
@endpush

@section('content')
    <div class="cardapio-form-container">
        <header class="cardapio-form-header">
            <div class="cardapio-form-header__text">
                <p class="cardapio-form-header__eyebrow">Cardápio • Edição</p>
                <h1 class="cardapio-form-header__title">
                    Atualizar: {{ $item['nome'] ?? 'Item do cardápio' }}
                </h1>
                <p class="cardapio-form-header__description">
                    Ajuste o conteúdo e mantenha a experiência do cliente sempre impecável.
                </p>
            </div>
            <a href="{{ route('cardapio.index') }}" class="cardapio-form-button cardapio-form-button--secondary">
                Voltar para o cardápio
            </a>
        </header>

        <section class="cardapio-form-highlight-grid" aria-label="Resumo do item">
            <article class="cardapio-form-highlight">
                <span><strong>Categoria</strong></span>
                <p>{{ $item['categoria'] ?? '—' }}</p>
            </article>
            <article class="cardapio-form-highlight">
                <span><strong>Preço atual</strong></span>
                <p>R$ {{ number_format($item['preco'] ?? 0, 2, ',', '.') }}</p>
            </article>
            <article class="cardapio-form-highlight">
                <span><strong>Última atualização</strong></span>
                <p>
                    @if (!empty($item['updatedAt']))
                        {{ \Carbon\Carbon::parse($item['updatedAt'])->format('d/m/Y H:i') }}
                    @elseif (!empty($item['createdAt']))
                        {{ \Carbon\Carbon::parse($item['createdAt'])->format('d/m/Y H:i') }}
                    @else
                        —
                    @endif
                </p>
            </article>
        </section>

        @include('cardapio._form', [
            'action' => route('cardapio.update', $item['id']),
            'method' => 'PUT',
            'buttonText' => 'Atualizar item',
            'item' => $item,
        ])
    </div>
@endsection


