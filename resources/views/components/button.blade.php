@props([
    'variant' => 'primary',
    'type' => 'button',
    'size' => 'md',
    'iconPosition' => 'left',
])

@php
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];

    $baseClasses = 'inline-flex items-center justify-center gap-2 rounded-lg font-semibold uppercase tracking-wide shadow-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2';
    $baseClasses .= ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);

    $variantClasses = [
        'primary' => 'border border-amber-500/80 bg-white text-amber-700 hover:border-amber-600 hover:text-amber-800 hover:bg-amber-50 focus-visible:ring-amber-500',
        'secondary' => 'border border-gray-300 bg-white text-gray-700 hover:border-gray-400 hover:text-gray-900 hover:bg-gray-100 focus-visible:ring-gray-300',
        'neutral' => 'border border-gray-200 bg-gray-50 text-gray-700 hover:border-gray-300 hover:text-gray-900 hover:bg-white focus-visible:ring-gray-200',
        'light' => 'border border-white/40 bg-white/10 text-white hover:border-white/60 hover:bg-white/20 focus-visible:ring-white/60',
        'danger' => 'border border-red-200 bg-red-50 text-red-600 hover:border-red-300 hover:text-red-700 hover:bg-red-100 focus-visible:ring-red-400',
        'success' => 'border border-green-200 bg-green-50 text-green-700 hover:border-green-300 hover:text-green-800 hover:bg-green-100 focus-visible:ring-green-400',
    ];

    $classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']);

    $tag = $attributes->has('href') ? 'a' : 'button';
    $iconSlot = isset($icon) ? $icon : null;
@endphp

@if ($tag === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }}>
        @if ($iconSlot && $iconPosition === 'left')
            {{ $iconSlot }}
        @endif

        {{ $slot }}

        @if ($iconSlot && $iconPosition === 'right')
            {{ $iconSlot }}
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($iconSlot && $iconPosition === 'left')
            {{ $iconSlot }}
        @endif

        {{ $slot }}

        @if ($iconSlot && $iconPosition === 'right')
            {{ $iconSlot }}
        @endif
    </button>
@endif

