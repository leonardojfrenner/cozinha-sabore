@php
    $nome = old('nome', $item['nome'] ?? '');
    $descricao = old('descricao', $item['descricao'] ?? '');
    $preco = old('preco', isset($item['preco']) ? number_format($item['preco'], 2, '.', '') : '');
    $categoria = old('categoria', $item['categoria'] ?? '');
    $imagemUrl = old('imagemUrl', $item['imagemUrl'] ?? '');
@endphp

@if ($errors->any())
    @php
        $mensagensPersonalizadas = [
            'The imagem failed to upload.' => 'Não foi possível enviar a imagem. Verifique se o arquivo está nos formatos JPG, PNG ou GIF e se possui no máximo 2 MB.',
            'The imagem must be a file of type: jpg, jpeg, png, gif.' => 'A imagem deve estar nos formatos JPG, JPEG, PNG ou GIF.',
            'The imagem may not be greater than 2048 kilobytes.' => 'A imagem deve ter no máximo 2 MB.',
        ];
    @endphp
    <div class="cardapio-form-error">
        <strong>Ops! Verifique os pontos abaixo:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $mensagensPersonalizadas[$error] ?? $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="cardapio-form">
    @csrf
    @if (strtoupper($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div class="cardapio-form-section">
        <header class="cardapio-form-section__header">
            <h2 class="cardapio-form-section__title">Informações principais</h2>
            <p class="cardapio-form-section__subtitle">
                Defina como o prato será apresentado aos clientes no cardápio digital.
            </p>
            @php
                $referenciaData = $item['updatedAt'] ?? $item['createdAt'] ?? null;
            @endphp
            @if ($referenciaData)
                <span class="cardapio-form-highlight" style="max-width: fit-content;">
                    Atualizado em {{ \Carbon\Carbon::parse($referenciaData)->format('d/m/Y H:i') }}
                </span>
            @endif
        </header>

        <div class="cardapio-form-grid">
            <div class="cardapio-form-field">
                <label for="nome">Nome *</label>
                <input
                    type="text"
                    name="nome"
                    id="nome"
                    value="{{ $nome }}"
                    required
                    autocomplete="off"
                    class="cardapio-form-input"
                >
                <p class="cardapio-form-help">Sugestão de preenchimento: Risoto de camarão com limão siciliano.</p>
            </div>

            <div class="cardapio-form-field">
                <label for="categoria">Categoria</label>
                <input
                    type="text"
                    name="categoria"
                    id="categoria"
                    value="{{ $categoria }}"
                    class="cardapio-form-input"
                >
                <p class="cardapio-form-help">Ajude a equipe e os clientes a encontrarem o prato com facilidade.</p>
            </div>

            <div class="cardapio-form-field" style="grid-column: 1 / -1;">
                <label for="preco">Preço *</label>
                <div class="cardapio-form-price-wrapper">
                    <span class="cardapio-form-price-prefix">R$</span>
                    <input
                        type="number"
                        step="0.01"
                        name="preco"
                        id="preco"
                        value="{{ $preco }}"
                        required
                        min="0"
                        inputmode="decimal"
                        class="cardapio-form-input cardapio-form-price-input"
                        placeholder="0,00"
                    >
                </div>
                <p class="cardapio-form-help">Use ponto para separar centavos (ex.: 29.90). O valor é atualizado automaticamente no cardápio.</p>
            </div>
        </div>
    </div>

    <div class="cardapio-form-section">
        <header class="cardapio-form-section__header">
            <h2 class="cardapio-form-section__title">História e ingredientes</h2>
            <p class="cardapio-form-section__subtitle">
                Descreva brevemente o prato, ingredientes principais e sugestões de harmonização.
            </p>
        </header>
        <div class="cardapio-form-field">
            <label for="descricao" class="sr-only">Descrição</label>
            <textarea
                name="descricao"
                id="descricao"
                rows="5"
                class="cardapio-form-textarea"
                placeholder="Use este espaço para destacar ingredientes, preparo e sugestões de harmonização."
            >{{ $descricao }}</textarea>
            <p class="cardapio-form-help">
                Esse texto aparece logo abaixo do nome do prato no cardápio. Utilize linguagem clara e apetitosa.
            </p>
        </div>
    </div>

    <div class="cardapio-form-section">
        <header class="cardapio-form-section__header">
            <h2 class="cardapio-form-section__title">Imagem do prato</h2>
            <p class="cardapio-form-section__subtitle">
                Uma boa foto valoriza o prato. Prefira imagens horizontais e bem iluminadas.
            </p>
        </header>

        <div class="cardapio-form-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
            <div class="cardapio-form-field">
                <label for="imagem">Upload de imagem</label>
                <input
                    type="file"
                    name="imagem"
                    id="imagem"
                    accept="image/*"
                    class="cardapio-form-upload"
                >
                <p class="cardapio-form-help">Formatos aceitos: JPG, PNG, GIF · Tamanho máximo: 2 MB.</p>
            </div>

            @php
                $imagemAtual = $item['imagemUrlCompleta'] ?? $item['imagemUrl'] ?? null;
            @endphp

            @if (!empty($imagemAtual))
                <div class="cardapio-form-preview">
                    <img
                        src="{{ $imagemAtual }}"
                        alt="Imagem atual do item"
                        onerror="this.remove();"
                    >
                    <div class="cardapio-form-preview__meta">
                        <p><strong>{{ $item['nome'] ?? 'Item do cardápio' }}</strong></p>
                        <p class="cardapio-form-help">{{ $imagemAtual }}</p>
                    </div>
                    <input type="hidden" name="imagemUrlAtual" value="{{ $item['imagemUrl'] ?? '' }}">
                </div>
            @endif
        </div>
    </div>

    <footer class="cardapio-form-footer">
        <div class="cardapio-form-footer__note">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            Campos marcados com * são obrigatórios.
        </div>
        <div class="cardapio-form-footer__actions">
            <a href="{{ route('cardapio.index') }}" class="cardapio-form-button cardapio-form-button--secondary">
                Cancelar
            </a>
            <button type="submit" class="cardapio-form-button cardapio-form-button--primary">
                {{ $buttonText }}
            </button>
        </div>
    </footer>
</form>


