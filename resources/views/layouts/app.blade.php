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
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @stack('styles')
</head>
<body class="dashboard-body">
    <header class="navbar">
        <div class="navbar__content">
            <div class="navbar__brand">
                <span class="navbar__title">Cozinha Sabore</span>
                @if(session('restaurante_logado'))
                    <span class="navbar__subtitle">Bem-vindo, {{ session('restaurante_logado')['nome'] }}</span>
                @endif
            </div>

            @if(session('restaurante_logado'))
                <button type="button" class="navbar__toggle" aria-label="Abrir menu" data-navbar-toggle>
                    <span class="navbar__toggle-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
                <nav class="navbar__links" data-navbar-menu>
                    <a href="{{ route('pedidos.index') }}" class="navbar__link {{ request()->routeIs('pedidos.index', 'pedidos.show', 'pedidos.edit') ? 'is-active' : '' }}">
                        Pedidos
                    </a>
                    <a href="{{ route('cardapio.index') }}" class="navbar__link {{ request()->routeIs('cardapio.*') ? 'is-active' : '' }}">
                        Cardápio
                    </a>
                    <a href="{{ route('pedidos.historico') }}" class="navbar__link {{ request()->routeIs('pedidos.historico') ? 'is-active' : '' }}">
                        Histórico
                    </a>
                    <form method="POST" action="{{ route('restaurante.logout') }}" class="navbar__logout-form">
                        @csrf
                        <button type="submit" class="navbar__link navbar__logout">
                            Sair
                        </button>
                    </form>
                </nav>
            @endif
        </div>
    </header>

    <main class="dashboard-main">
        <div class="dashboard-container">
            @if(session('success'))
                <div class="alert alert--success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert--error">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <div class="confirm-modal__overlay" data-confirm-overlay hidden>
        <div class="confirm-modal" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title">
            <div class="confirm-modal__icon" aria-hidden="true">⚠️</div>
            <h2 class="confirm-modal__title" id="confirm-modal-title">Confirmar ação</h2>
            <p class="confirm-modal__message" data-confirm-message>Tem certeza que deseja continuar?</p>
            <div class="confirm-modal__actions">
                <button type="button" class="confirm-modal__button confirm-modal__button--ghost" data-confirm-cancel>
                    Cancelar
                </button>
                <button type="button" class="confirm-modal__button confirm-modal__button--primary" data-confirm-accept>
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.querySelector('[data-navbar-toggle]');
            const menu = document.querySelector('[data-navbar-menu]');

            if (toggle && menu) {
                toggle.addEventListener('click', () => {
                    menu.classList.toggle('is-open');
                    toggle.classList.toggle('is-open');
                });
            }

            const forms = document.querySelectorAll('.update-status-form');
            forms.forEach(form => {
                form.addEventListener('submit', (event) => {
                    if (event.defaultPrevented) {
                        return;
                    }

                    const button = form.querySelector('button[type="submit"]');
                    if (!button) return;

                    const spinner = button.querySelector('.loading-spinner');
                    const buttonText = button.querySelector('.button-text');
                    const otherIcons = button.querySelectorAll('.icon-normal');

                    button.disabled = true;

                    if (spinner) {
                        spinner.classList.remove('hidden');
                    }
                    otherIcons.forEach(icon => icon.classList.add('hidden'));

                    if (buttonText) {
                        buttonText.textContent = 'Atualizando...';
                    }
                });
            });

            const confirmOverlay = document.querySelector('[data-confirm-overlay]');
            if (!confirmOverlay) {
                return;
            }

            const confirmMessageEl = confirmOverlay.querySelector('[data-confirm-message]');
            const confirmAcceptBtn = confirmOverlay.querySelector('[data-confirm-accept]');
            const confirmCancelBtn = confirmOverlay.querySelector('[data-confirm-cancel]');

            let confirmResolver = null;
            let previousActiveElement = null;

            const hideOverlay = () => {
                if (!confirmOverlay.classList.contains('is-visible')) {
                    confirmOverlay.setAttribute('hidden', '');
                    return;
                }

                const handleTransitionEnd = (event) => {
                    if (event.target !== confirmOverlay) return;
                    confirmOverlay.setAttribute('hidden', '');
                    confirmOverlay.removeEventListener('transitionend', handleTransitionEnd);
                };

                confirmOverlay.addEventListener('transitionend', handleTransitionEnd, { once: true });
                confirmOverlay.classList.remove('is-visible');
            };

            const closeConfirm = (approved) => {
                hideOverlay();

                if (confirmResolver) {
                    confirmResolver(approved);
                    confirmResolver = null;
                }

                if (previousActiveElement && typeof previousActiveElement.focus === 'function') {
                    previousActiveElement.focus();
                }
            };

            const openConfirm = (message) => {
                if (confirmResolver) {
                    return Promise.resolve(false);
                }

                confirmMessageEl.textContent = message || 'Tem certeza que deseja continuar?';
                previousActiveElement = document.activeElement instanceof HTMLElement ? document.activeElement : null;
                confirmOverlay.removeAttribute('hidden');
                requestAnimationFrame(() => {
                    confirmOverlay.classList.add('is-visible');
                    confirmAcceptBtn.focus();
                });

                return new Promise((resolve) => {
                    confirmResolver = resolve;
                });
            };

            confirmAcceptBtn.addEventListener('click', () => closeConfirm(true));
            confirmCancelBtn.addEventListener('click', () => closeConfirm(false));

            confirmOverlay.addEventListener('click', (event) => {
                if (event.target === confirmOverlay) {
                    closeConfirm(false);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && confirmOverlay.classList.contains('is-visible')) {
                    closeConfirm(false);
                }
            });

            document.addEventListener('submit', async (event) => {
                const form = event.target;

                if (!(form instanceof HTMLFormElement)) {
                    return;
                }

                const message = form.getAttribute('data-confirm');
                if (!message) {
                    return;
                }

                if (form.dataset.confirmed === 'true') {
                    delete form.dataset.confirmed;
                    return;
                }

                event.preventDefault();
                event.stopImmediatePropagation();

                const approved = await openConfirm(message);

                if (approved) {
                    form.dataset.confirmed = 'true';
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        setTimeout(() => form.submit(), 0);
                    }
                }
            }, true);
        });
    </script>
</body>
</html>
