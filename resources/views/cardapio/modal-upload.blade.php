<div
    x-data="{ open: false }"
    x-init="$watch('open', value => { if (value) { setTimeout(() => $refs.nome?.focus(), 50); } })"
    class="relative inline-block"
>
    <x-button
        type="button"
        variant="primary"
        size="md"
        @click.prevent="open = true"
    >
        Novo item
    </x-button>

    <div
        x-cloak
        x-show="open"
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-30 flex items-center justify-center bg-black/50 px-4"
    >
        <div
            @click.away="open = false"
            class="bg-white rounded-xl shadow-2xl max-w-3xl w-full overflow-hidden"
        >
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Adicionar novo item</h2>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                    <span class="sr-only">Fechar</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('cardapio.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="modal-nome" class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                            <input
                                x-ref="nome"
                                type="text"
                                name="nome"
                                id="modal-nome"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                            >
                        </div>

                        <div>
                            <label for="modal-categoria" class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                            <input
                                type="text"
                                name="categoria"
                                id="modal-categoria"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                placeholder="Ex.: Prato quente, Salada, Bebida"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="modal-preco" class="block text-sm font-medium text-gray-700 mb-1">Preço *</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="preco"
                                id="modal-preco"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="modal-descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                        <textarea
                            name="descricao"
                            id="modal-descricao"
                            rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                            placeholder="Liste ingredientes ou detalhes do prato."
                        ></textarea>
                    </div>

                    <div class="border border-dashed border-gray-300 rounded-lg p-4 bg-gray-50">
                        <label for="modal-imagem" class="block text-sm font-medium text-gray-700 mb-1">Imagem do prato</label>
                        <input
                            type="file"
                            name="imagem"
                            id="modal-imagem"
                            accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer focus:outline-none focus:border-amber-500 focus:ring-amber-500"
                        >
                        <p class="text-xs text-gray-500 mt-1">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2 MB.</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <x-button
                            type="button"
                            variant="secondary"
                            size="sm"
                            @click="open = false"
                        >
                            Cancelar
                        </x-button>
                        <x-button
                            type="submit"
                            variant="primary"
                            size="md"
                        >
                            Cadastrar
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

