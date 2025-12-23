@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, danger, success, warning, ghost
    'size' => 'md', // sm, md, lg
    'fullWidth' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed';
    
    $variantClasses = [
        'primary' => 'rounded-md bg-primary-600 text-white shadow-xs hover:bg-primary-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600',
        'secondary' => 'rounded-md bg-gray-200 text-gray-900 hover:bg-gray-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-500',
        'danger' => 'rounded-md bg-error-600 text-white shadow-xs hover:bg-error-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-error-500',
        'success' => 'rounded-md bg-success-600 text-white shadow-xs hover:bg-success-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-success-500',
        'warning' => 'rounded-md bg-warning-600 text-white shadow-xs hover:bg-warning-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-warning-500',
        'ghost' => 'rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-500',
    ];
    
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm/6',
        'md' => 'px-3 py-1.5 text-sm/6',
        'lg' => 'px-4 py-2 text-base',
    ];
    
    $classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size];
    
    if ($fullWidth) {
        $classes .= ' w-full justify-center';
    }
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>

