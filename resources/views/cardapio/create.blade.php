@extends('layouts.app')

@section('title', 'Novo item do cardápio')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cardapio-form.css') }}">
@endpush

@section('content')
    <div class="cardapio-form-container">
        <header class="cardapio-form-header">
            <div class="cardapio-form-header__text">
                <p class="cardapio-form-header__eyebrow">Cardápio • Novo item</p>
                <h1 class="cardapio-form-header__title">Cadastrar novo prato</h1>
                <p class="cardapio-form-header__description">
                    Em poucos passos você publica um prato com descrição, imagem e preço alinhados com o cardápio digital.
                </p>
            </div>
            <a href="{{ route('cardapio.index') }}" class="cardapio-form-button cardapio-form-button--secondary">
                Voltar para o cardápio
            </a>
        </header>

        <section class="cardapio-form-highlight-grid" aria-label="Etapas de preenchimento">
            <article class="cardapio-form-highlight">
                <span><strong>Etapa 1</strong></span>
                <span>Informações principais</span>
                <p>Nome, categoria e preço para o cliente entender rapidamente o prato.</p>
            </article>
            <article class="cardapio-form-highlight">
                <span><strong>Etapa 2</strong></span>
                <span>História e descrição</span>
                <p>Conte uma história apetitosa com ingredientes e diferenciais.</p>
            </article>
            <article class="cardapio-form-highlight">
                <span><strong>Etapa 3</strong></span>
                <span>Imagem do prato</span>
                <p>Adicione uma foto nítida e atual para reforçar a qualidade.</p>
            </article>
        </section>

        <div class="cardapio-form-alert">
            <span class="cardapio-form-alert__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                </svg>
            </span>
            <div>
                <strong>Dica rápida</strong>
                <p>
                    Títulos claros e descrições com ingredientes principais facilitam a venda.
                    Se tiver harmonizações sugeridas, inclua na descrição.
                </p>
            </div>
        </div>

        @include('cardapio._form', [
            'action' => route('cardapio.store'),
            'method' => 'POST',
            'buttonText' => 'Salvar item',
            'item' => $item ?? [],
        ])
    </div>
@endsection


